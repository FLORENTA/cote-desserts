import Vue from 'vue';
import VueRouter from 'vue-router';
import {Routing} from './../js/routing';

/* Components */
import Admin from '../components/admin/Admin';
import AdminComment from '../components/admin/AdminComment';
import AdminContact from '../components/admin/AdminContact';
import AdminCreateArticle from '../components/admin/AdminCreateArticle';
import AdminEditArticle from '../components/admin/AdminEditArticle';
import AdminHomepage from '../components/admin/AdminHomepage';
import AdminLegal from '../components/admin/AdminLegal';
import AdminLogin from '../components/admin/AdminLogin';
import AdminNewsletter from '../components/admin/AdminNewsletter';
import AdminPassword from "../components/admin/AdminPassword";
import AdminStatistic from '../components/admin/AdminStatistic';
import PageNotFound from '../components/PageNotFound';
import User from '../components/user/User';
import UserArticle from '../components/user/UserArticle';
import UserCategory from '../components/user/UserCategory';
import UserContact from '../components/user/UserContact';
import UserHomepage from '../components/user/UserHomepage';
import UserLegal from '../components/user/UserLegal';

Vue.use(VueRouter);

let routes = [
    {
        path: '/admin',
        name: 'home_admin',
        component: Admin,
        children: [
            {
                path: 'articles',
                name: 'homepageAdmin',
                component: AdminHomepage,
                props: true
            },
            {
                path: 'articles/edit/:token',
                name: 'editArticle',
                component: AdminEditArticle,
                props: true
            },
            {
                path: 'comments',
                name: 'comments',
                component: AdminComment
            },
            {
                path: 'contacts',
                name: 'contacts',
                component: AdminContact
            },
            {
                path: 'create',
                name: 'createArticle',
                component: AdminCreateArticle,
            },
            {
                path: 'legal',
                name: 'admin-legal',
                component: AdminLegal
            },
            {
                path: 'logout',
                name: 'logout'
            },
            {
                path: 'password',
                name: 'password',
                component: AdminPassword
            },
            {
                path: 'newsletter',
                name: 'newsletter',
                component: AdminNewsletter
            },
            {
                path: 'statistic',
                name: 'statistic',
                component: AdminStatistic
            }
        ],
        // all routes above requires authentication
        meta: {
            requiresAuth: true,
        }
    },
    {
        path: '/login',
        name: 'login',
        component: AdminLogin,
    },
    {
        path: '/',
        component: User,
        children: [
            {
                path: '',
                name: 'home_user',
                component: UserHomepage,
                props: {default: true}
            },
            {
                path: '/article/:slug',
                name: 'article',
                component: UserArticle,
                props: true
            },
            {
                path: '/category',
                name: 'categories',
                component: UserCategory,
                props: true
            },
            {
                path: '/category/:category',
                name: 'category',
                component: UserCategory,
                props: true
            },
            {
                path: 'contact',
                name: 'contact',
                component: UserContact
            },
            {
                path: '/mentions-legales',
                name: 'user-legal',
                component: UserLegal
            },
            {
                path: '*',
                component: PageNotFound
            }
        ]
    }
];

/* Registering the app routes */
const Router = new VueRouter({
    routes: routes
});

/* Global navigation guard */
Router.beforeEach((to, from, next) => {
    /* Getting routes matching the current one, including the parent route */
    /* Then, only the parent route may contain the requiresAuth meta property */
    const requiresAuth = to.matched.some(record => {
        return record.meta.requiresAuth;
    });

    if (requiresAuth) {
        userAuthenticated().then(data => {
            next();
        }).catch(err => {
            next('/login');
        });
    } else {
        next();
    }
});

/* Ajax request to get the token stored in session */
function userAuthenticated() {
    return new Promise((resolve, reject) => {
        if (!localStorage.getItem('token')) {
            reject(Translator.trans('login.token.local_storage.unknown'));
        }

        $.get(Routing.generate('fetch_session_token'), response => {
            /* If the token received is the same as the
               one stored in localStorage (received when identifying
               for the first time) : Authentication is ok
               Else, possible errors : token not set in session
               Or fake token added thanks to basic console command =>
               will result in invalid token message
             */
            if (localStorage.getItem('token') === response) {
                resolve(Translator.trans('login.authentication.success'));
            } else {
                reject(Translator.trans('login.token.invalid'));
            }
        }).fail(err => {
            reject(err.responseJSON)
        });
    });
}

export {Router};