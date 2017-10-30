import Vue from 'vue';
import Router from 'vue-router';
import Movies from '../components/Movies.vue';
import Movie from '../components/Movie.vue';

Vue.use(Router);

export default new Router({
  routes: [
    {
      path: '/',
      name: 'Movies',
      component: Movies,
      props: true,
    },
    {
      path: '/:id',
      name: 'Movie',
      component: Movie,
      props: true,
    },
  ],
});
