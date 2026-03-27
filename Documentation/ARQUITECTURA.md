# Arquitectura del Sistema de Quiniela FIFA 2026

## Diagrama de Arquitectura General

```mermaid
graph TB
    subgraph "Frontend"
        A[Web Browser] --> B[Blade Views]
        A --> C[JavaScript/Vue]
        B --> D[Vite Build]
        C --> D
    end

    subgraph "Presentation Layer"
        E[HTTP Controllers]
        F[API Controllers]
        G[Console Commands]
        H[Middleware]
    end

    subgraph "Application Layer"
        I[Commands]
        J[Queries]
        K[DTOs]
        L[Event Handlers]
    end

    subgraph "Domain Layer"
        M[Auth Domain]
        N[User Domain]
        O[Quiniela Domain]
        P[Match Domain]
        Q[Prediction Domain]
        R[Scoring Domain]
    end

    subgraph "Infrastructure Layer"
        S[Sanctum Auth]
        T[Fortify Auth]
        U[Permission System]
        V[Eloquent Repositories]
        W[Audit Logger]
        X[Pusher Notifications]
        Y[FIFA API Client]
    end

    subgraph "External Services"
        Z[FIFA API]
        AA[Pusher Service]
        AB[Email Service]
    end

    subgraph "Data Layer"
        AC[(MySQL Database)]
        AD[(Redis Cache)]
        AE[File Storage]
    end

    A --> E
    A --> F
    E --> I
    E --> J
    F --> I
    F --> J
    I --> M
    I --> N
    I --> O
    I --> P
    I --> Q
    I --> R
    J --> M
    J --> N
    J --> O
    J --> P
    J --> Q
    J --> R
    M --> S
    M --> T
    N --> U
    O --> V
    P --> V
    Q --> V
    R --> V
    V --> AC
    V --> AD
    W --> AE
    X --> AA
    Y --> Z
    L --> W
    L --> X
    L --> AB
```

## Diagrama de Flujo de Autenticación

```mermaid
sequenceDiagram
    participant U as Usuario
    participant F as Fortify
    participant S as Sanctum
    participant P as Permission
    participant DB as Database

    U->>F: POST /login
    F->>F: Validar credenciales
    F->>DB: Verificar usuario
    DB-->>F: Usuario válido
    F->>S: Crear token API
    S-->>F: Token generado
    F->>P: Cargar roles/permisos
    P-->>F: Permisos cargados
    F-->>U: Respuesta con token + permisos

    U->>S: Request con Bearer Token
    S->>S: Validar token
    S->>P: Verificar permisos
    P-->>S: Permisos válidos
    S-->>U: Acceso concedido
```

## Diagrama de Flujo de Predicciones

```mermaid
sequenceDiagram
    participant U as Usuario
    participant C as Controller
    participant S as PredictionService
    participant V as Validator
    participant DB as Database
    participant L as AuditLogger
    participant N as Notification

    U->>C: POST /predictions
    C->>V: Validar datos
    V-->>C: Datos válidos
    C->>S: Crear predicción
    S->>DB: Guardar predicción
    DB-->>S: Predicción guardada
    S->>L: Log de auditoría
    L-->>S: Log registrado
    S->>N: Notificar evento
    N-->>S: Notificación enviada
    S-->>C: Predicción creada
    C-->>U: Respuesta exitosa
```

## Diagrama de Cálculo de Puntuación

```mermaid
flowchart TD
    A[Partido Finalizado] --> B{Obtener Resultado Real}
    B --> C[Obtener Predicciones]
    C --> D{Para cada predicción}
    D --> E[Comparar resultado]
    E --> F{¿Resultado exacto?}
    F -->|Sí| G[+10 puntos]
    F -->|No| H{¿Ganador correcto?}
    H -->|Sí| I[+5 puntos]
    H -->|No| J{¿Diferencia correcta?}
    J -->|Sí| K[+3 puntos]
    J -->|No| L{¿Goles equipo?}
    L -->|Sí| M[+2 puntos]
    L -->|No| N[0 puntos]
    G --> O[Calcular total]
    I --> O
    K --> O
    M --> O
    N --> O
    O --> P[Actualizar Score]
    P --> Q[Actualizar Leaderboard]
    Q --> R[Notificar usuario]
```

## Diagrama de Estructura DDD

```mermaid
graph LR
    subgraph "Domain"
        A[Auth]
        B[User]
        C[Quiniela]
        D[Match]
        E[Prediction]
        F[Scoring]
    end

    subgraph "Application"
        G[Commands]
        H[Queries]
        I[DTOs]
        J[Events]
    end

    subgraph "Infrastructure"
        K[Sanctum]
        L[Fortify]
        M[Permissions]
        N[Eloquent]
        O[Logger]
        P[Pusher]
    end

    subgraph "Presentation"
        Q[HTTP Controllers]
        R[API Controllers]
        S[Console]
    end

    A --> K
    A --> L
    B --> M
    C --> N
    D --> N
    E --> N
    F --> N
    G --> A
    G --> B
    G --> C
    G --> D
    G --> E
    G --> F
    H --> A
    H --> B
    H --> C
    H --> D
    H --> E
    H --> F
    Q --> G
    Q --> H
    R --> G
    R --> H
    J --> O
    J --> P
```

## Diagrama de Base de Datos

```mermaid
erDiagram
    USERS ||--o{ PERSONAL_ACCESS_TOKENS : has
    USERS ||--o{ PREDICTIONS : makes
    USERS ||--o{ SCORES : earns
    USERS }o--o{ ROLES : has
    USERS }o--o{ PERMISSIONS : has
    ROLES ||--o{ PERMISSIONS : has
    QUIZINELAS ||--o{ MATCHES : contains
    QUIZINELAS ||--o{ USERS : has_participants
    QUIZINELAS ||--o{ LEADERBOARDS : has
    MATCHES ||--o{ PREDICTIONS : has
    MATCHES }o--o{ TEAMS : involves
    MATCHES ||--o{ STAGES : belongs_to
    PREDICTIONS ||--o{ SCORES : generates
    STAGES ||--o{ GROUPS : has
    TEAMS ||--o{ GROUPS : belongs_to

    USERS {
        id bigint PK
        name string
        email string
        password string
        email_verified_at timestamp
        remember_token string
        created_at timestamp
        updated_at timestamp
    }

    QUIZINELAS {
        id bigint PK
        name string
        description text
        start_date date
        end_date date
        status enum
        created_by bigint FK
        created_at timestamp
        updated_at timestamp
    }

    MATCHES {
        id bigint PK
        quiniela_id bigint FK
        home_team_id bigint FK
        away_team_id bigint FK
        stage_id bigint FK
        match_date datetime
        home_score int
        away_score int
        status enum
        created_at timestamp
        updated_at timestamp
    }

    TEAMS {
        id bigint PK
        name string
        code string
        flag_url string
        group_id bigint FK
        created_at timestamp
        updated_at timestamp
    }

    PREDICTIONS {
        id bigint PK
        user_id bigint FK
        match_id bigint FK
        home_score int
        away_score int
        points_earned int
        created_at timestamp
        updated_at timestamp
    }

    SCORES {
        id bigint PK
        user_id bigint FK
        quiniela_id bigint FK
        total_points int
        correct_predictions int
        exact_predictions int
        position int
        created_at timestamp
        updated_at timestamp
    }

    LEADERBOARDS {
        id bigint PK
        quiniela_id bigint FK
        user_id bigint FK
        position int
        total_points int
        stage enum
        created_at timestamp
        updated_at timestamp
    }

    STAGES {
        id bigint PK
        quiniela_id bigint FK
        name string
        type enum
        order int
        start_date date
        end_date date
        created_at timestamp
        updated_at timestamp
    }

    GROUPS {
        id bigint PK
        stage_id bigint FK
        name string
        created_at timestamp
        updated_at timestamp
    }

    AUDIT_LOGS {
        id bigint PK
        user_id bigint FK
        action string
        model_type string
        model_id bigint
        old_values json
        new_values json
        ip_address string
        user_agent text
        created_at timestamp
    }
```

## Diagrama de Componentes del Sistema

```mermaid
graph TB
    subgraph "Authentication"
        A1[Sanctum]
        A2[Fortify]
        A3[Permission]
    end

    subgraph "Core Domains"
        B1[Auth Domain]
        B2[User Domain]
        B3[Quiniela Domain]
        B4[Match Domain]
        B5[Prediction Domain]
        B6[Scoring Domain]
    end

    subgraph "Services"
        C1[Prediction Service]
        C2[Scoring Service]
        C3[Leaderboard Service]
        C4[Notification Service]
    end

    subgraph "Infrastructure"
        D1[Eloquent ORM]
        D2[Redis Cache]
        D3[Audit Logger]
        D4[Pusher]
    end

    subgraph "External"
        E1[FIFA API]
        E2[Email Service]
    end

    A1 --> B1
    A2 --> B1
    A3 --> B2
    B1 --> C1
    B2 --> C1
    B3 --> C1
    B4 --> C1
    B5 --> C1
    B6 --> C1
    C1 --> D1
    C2 --> D1
    C2 --> D2
    C3 --> D1
    C3 --> D2
    C4 --> D4
    C4 --> E2
    D1 --> D3
    B4 --> E1
```

## Referencias de Arquitectura

### Principios DDD Aplicados
1. **Separación de Responsabilidades**: Cada capa tiene una función específica
2. **Domain Logic en el Dominio**: La lógica de negocio reside en la capa de dominio
3. **Infrastructure Independence**: El dominio no depende de la infraestructura
4. **Application Services**: Orquestan casos de uso sin lógica de negocio
5. **Value Objects**: Representan conceptos inmutables del dominio

### Patrones Utilizados
- **Repository Pattern**: Abstracción de acceso a datos
- **Command Query Responsibility Segregation (CQRS)**: Separación de lectura y escritura
- **Event Sourcing**: Registro de eventos para auditoría
- **Domain Events**: Comunicación entre bounded contexts
- **DTOs**: Transferencia de datos entre capas

### Convenciones de Código
- **PSR-12**: Estilo de código PHP
- **Laravel Pint**: Formateo automático
- **Type Hints**: Tipado estricto en PHP
- **PHPDoc**: Documentación de código
- **SOLID Principles**: Principios de diseño orientado a objetos
