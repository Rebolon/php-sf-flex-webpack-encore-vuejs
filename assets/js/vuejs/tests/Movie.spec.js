import Vue from 'vue'
import Movie from '../components/Movie'
import Loader from '../components/Loader'
import router from '../router'

// configure Vue instance
Vue.component('Loader', Loader)

const defaultMsg = 'Detail of the movie'

describe('Movie', () => {
    // Inspect object options of component
    it('has the hook `created`', () => {
        expect(typeof Movie.created).toBe('function')
    })

    // Evaluate function results from component
    it('has default data', () => {
        expect(typeof Movie.data).toBe('function')
        const defaultData = Movie.data()
        expect(defaultData.msg).toBe(defaultMsg)
        expect(defaultData.isLoading).toBe(true)
    })

    // Inspect instance mounting of component
    it('is finely mounted', () => {
        const Constructor = Vue.extend(Movie)
        const vm = new Constructor({router}).$mount()
        expect(vm.isLoading).toBe(false)
        expect(vm.$el.firstChild.textContent.trim()).toBe('No movie to display')
    })

    // Mount an instance and inspect the output result
    it('is finely rendered with an id', () => {
        const Constructor = Vue.extend(Movie)
        const vm = new Constructor({router, propsData: {id: '2baf70d1-42bb-4437-b551-e5fed5a87abe'}}).$mount()
        expect(vm.$el.firstChild.textContent.trim()).toBe(defaultMsg)
    })

    // Mount an instance and inspect the output result
    it('is finely rendered with a movie', () => {
        const propMovie = {
            id: 1,
            title: 'The best movie ever',
            description: 'A great movie with fun, sport, enigmas...',
            release_date: '2010',
        }
        const Constructor = Vue.extend(Movie)
        const vm = new Constructor({
            router,
            propsData: {
                aMovie: propMovie
            }
        }).$mount()
        const h1 = vm.$el.querySelector('h1')
        const div = h1.nextElementSibling
        expect(h1.textContent).toBe(defaultMsg)
        expect(div.textContent).toBe(`"${propMovie.title}" : ${propMovie.description} The movie has been released in ${propMovie.release_date}`)
    })
})
