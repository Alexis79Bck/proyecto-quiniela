import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
    forceTLS: true,
    encrypted: true,
});

/**
 * Suscribirse a canales de notificaciones
 */
export function subscribeToNotifications(userId) {
    // Canal público para quinielas
    window.Echo.channel('quinielas')
        .listen('.new-quiniela-available', (e) => {
            console.log('Nueva quiniela disponible:', e);
            showNotification(e.message, 'info');
        })
        .listen('.winners-announced', (e) => {
            console.log('Ganadores anunciados:', e);
            showNotification(e.message, 'success');
        });

    // Canal público para partidos
    window.Echo.channel('matches')
        .listen('.match-started', (e) => {
            console.log('Partido iniciado:', e);
            showNotification(e.message, 'info');
        })
        .listen('.match-result-available', (e) => {
            console.log('Resultado de partido:', e);
            showNotification(e.message, 'info');
        });

    // Canal privado para el usuario (solo eventos específicos del usuario)
    window.Echo.private(`user.${userId}`)
        .listen('.leaderboard-updated', (e) => {
            console.log('Clasificación actualizada (usuario):', e);
            showNotification(e.message, 'info');
        })
        .listen('.prediction-reminder', (e) => {
            console.log('Recordatorio de predicción (usuario):', e);
            showNotification(e.message, 'warning');
        });
}

/**
 * Suscribirse a canal de leaderboard de una quiniela específica
 */
export function subscribeToLeaderboard(quinielaId) {
    window.Echo.channel(`leaderboard.${quinielaId}`)
        .listen('.leaderboard-updated', (e) => {
            console.log('Clasificación actualizada:', e);
            showNotification(e.message, 'info');
        });
}

/**
 * Mostrar notificación en el frontend
 */
function showNotification(message, type = 'info') {
    // Crear elemento de notificación
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 ${getNotificationClass(type)}`;
    notification.innerHTML = `
        <div class="flex items-center">
            <span class="mr-2">${getNotificationIcon(type)}</span>
            <span>${message}</span>
            <button class="ml-4 text-white hover:text-gray-200" onclick="this.parentElement.parentElement.remove()">
                &times;
            </button>
        </div>
    `;

    // Agregar al DOM
    document.body.appendChild(notification);

    // Remover después de 5 segundos
    setTimeout(() => {
        if (notification.parentElement) {
            notification.remove();
        }
    }, 5000);
}

/**
 * Obtener clase CSS según tipo de notificación
 */
function getNotificationClass(type) {
    const classes = {
        'info': 'bg-blue-500 text-white',
        'success': 'bg-green-500 text-white',
        'warning': 'bg-yellow-500 text-white',
        'error': 'bg-red-500 text-white',
    };
    return classes[type] || classes['info'];
}

/**
 * Obtener icono según tipo de notificación
 */
function getNotificationIcon(type) {
    const icons = {
        'info': 'ℹ️',
        'success': '✅',
        'warning': '⚠️',
        'error': '❌',
    };
    return icons[type] || icons['info'];
}
