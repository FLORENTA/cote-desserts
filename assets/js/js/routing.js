const routes = require('../../../web/js/fos_js_routes.json');
import Routing from '../../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js';
Routing.setRoutingData(routes);
export {Routing};

if (window.location.protocol === 'http:') {
    window.location.href = window.location.href.replace(/http/, 'https');
}