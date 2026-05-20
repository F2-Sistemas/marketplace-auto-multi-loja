// Backoffice route middleware — protects all routes except /login
export default defineNuxtRouteMiddleware((to) => {
    // Skip on server-side (localStorage not available)
    if (!import.meta.client) return;

    // Allow the login page freely
    if (to.path === '/login') return;

    const token = localStorage.getItem('backoffice_auth_token');
    if (!token) {
        return navigateTo('/login');
    }
});
