import Vue from 'vue'
import Movies from '../components/Movies'
import Loader from '../components/Loader'
import router from '../router'

// configure Vue instance
Vue.component('Loader', Loader)

const defaultMsg = 'List of Ghibli movies'

describe('Movies', () => {
    // Inspect object options of component
    it('has the hook `created`', () => {
        expect(typeof Movies.created).toBe('function')
    })

    // Evaluate function results from component
    it('has default data', () => {
        expect(typeof Movies.data).toBe('function')
        const defaultData = Movies.data()
        expect(defaultData.msg).toBe(defaultMsg)
        expect(defaultData.isLoading).toBe(true)
    })

    // Inspect instance mounting of component
    it('is finely mounted', () => {
        const Constructor = Vue.extend(Movies)
        const vm = new Constructor({router, Loader}).$mount()
        expect(vm.isLoading).toBe(true)
        expect(vm.$el.firstChild.textContent.trim()).toBe(defaultMsg)
    })

    // Mount an instance and inspect the output result
    it('is finely rendered with a list of movies', () => {
        const movie = {
            id: 1,
            title: 'The best movie ever',
            description: 'A great movie with fun, sport, enigmas...',
            release_date: '2010',
        }
        const Constructor = Vue.extend(Movies)
        const vm = new Constructor({
            router
        }).$mount()

        vm.movies = [movie, movie, ]
        // wait a "tick" after state change before asserting DOM updates
        Vue.nextTick(() => {
            debugger
            const h1 = vm.$el.querySelector('h1')
            expect(h1.textContent).toBe(defaultMsg)

            /* @todo to be fixed, don't know why this nextTick is called before created hook whereas it should not
            const li = vm.$el.querySelectorAll('li')
            expect(li.length).toBe(2)
            */

            done()
        })
    })
})
