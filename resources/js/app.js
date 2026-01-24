import './bootstrap';
import { createApp } from 'vue';
import App from './components/App.vue';
import router, { setInitialData } from './router';

// Initialize Vue app when DOM is ready
function initVue() {
    console.log('Initializing Vue app...');
    const appElement = document.getElementById('app');
    
    if (!appElement) {
        console.error('App element (#app) not found in DOM!');
        return;
    }
    
    try {
        const propsData = appElement.dataset.props ? JSON.parse(appElement.dataset.props) : {};
        console.log('Props data loaded:', propsData);
        
        // Set initial data for router
        setInitialData(propsData);
        
        const app = createApp(App, {
            shopDomain: propsData.shopDomain || '',
            appName: propsData.appName || 'Shopify App'
        });
        
        app.use(router);
        app.mount('#app');
        console.log('Vue app mounted successfully');
    } catch (error) {
        console.error('Error mounting Vue app:', error);
        console.error('Error stack:', error.stack);
        console.error('Props data string:', appElement.dataset.props);
        
        // Show error in the DOM
        appElement.innerHTML = `
            <div style="padding: 20px; color: red;">
                <h2>Error loading Vue app</h2>
                <p>${error.message}</p>
                <p>Check the browser console for more details.</p>
            </div>
        `;
    }
}

// Wait for DOM to be ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initVue);
} else {
    // DOM is already ready
    initVue();
}
