/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

import axios from "axios";
window.axios = axios;

window.axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";

// Bootstrap
import * as Popper from "@popperjs/core";
window.Popper = Popper;
import * as bootstrap from "bootstrap";
window.bootstrap = bootstrap;

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

// import Echo from "laravel-echo";

// import Larasocket from "larasocket-js";
// window.Echo = new Echo({
//   broadcaster: Larasocket,
//   token: import.meta.env.VITE_LARASOCKET_TOKEN,
// });

// import Pusher from "pusher-js";
// window.Pusher = Pusher;

// window.Echo = new Echo({
//   broadcaster: "pusher",
//   key: import.meta.env.VITE_PUSHER_APP_KEY,
//   cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
//   encrypted: true,
//   wsHost: window.location.hostname,
//   wsPort: 6001,
//   wssPort: 6001,
//   disableStats: true,
//   forceTLS: false,
//   enabledTransports: ["ws", "wss"],
//   disabledTransports: ["sockjs", "xhr_polling", "xhr_streaming"],
// });
