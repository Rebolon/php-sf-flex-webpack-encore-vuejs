import Vue from 'vue'
import Movie from '../components/Movie'

describe('Movie', () => {
    // Inspecter l'objet d'options du composant
    it('has the hook `created`', () => {
        expect(typeof Movie.created).toBe('function')
    })

    // Évaluer les résultats des fonctions dans
    // l'objet d'options du composant
    it('has default data', () => {
        expect(typeof Movie.data).toBe('function')
        const defaultData = Movie.data()
        expect(Movie.msg).toBe('Detail of the movie')
        expect(Movie.movie).toBe('object')
        expect(Movie.isLoading).toBe(true)
    })

    // Inspecter l'instance au montage du composant
    it('is finely mounted', () => {
        const vm = new Vue(Movie).$mount()
        expect(vm.isLoading).toBe(false)
    })

    // Monter une instance et inspecter le résultat en sortie
    it('is finely rendered', () => {
        const Constructor = Vue.extend(Movie)
        const vm = new Constructor({id: '2baf70d1-42bb-4437-b551-e5fed5a87abe'}).$mount()
        expect(vm.$el.textContent).toBe('Detail of the movie')
    })
})
