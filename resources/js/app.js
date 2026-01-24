import './bootstrap';
import { createApp } from 'vue';
import Welcome from './components/Welcome.vue';

// Initialize Vue app when DOM is ready
function initVue() {
    console.log('Initializing Vue app...');
    const appElement = document.getElementById('app');
    console.log('App element found:', appElement);
    
    if (appElement) {
        try {
            const propsData = appElement.dataset.props ? JSON.parse(appElement.dataset.props) : {};
            console.log('Props data:', propsData);
            const app = createApp(Welcome, propsData);
            app.mount('#app');
            console.log('Vue app mounted successfully');
        } catch (error) {
            console.error('Error mounting Vue app:', error);
            console.error('Props data string:', appElement.dataset.props);
        }
    } else {
        console.error('App element not found!');
    }
}

// Wait for DOM to be ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initVue);
} else {
    // DOM is already ready
    initVue();
}
