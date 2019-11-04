const on = addEventListener, off = removeEventListener
const emit = (t, d) => dispatchEvent (new CustomEvent (t, {'detail': d}))

class App {

    constructor () {

        let ready = false
        let page = null

        this.error = new Error ('error')
        this.items = new Items ('articles')
        this.item = new Item ('article')

        this.items.onclose (this.update)

        on ('error', ev => {

            this.error.show (ev.detail)
        })

        on ('update.end', () => {

            Router.navigate ('articles/1')

            if (page !== null && page == location.hash.replace ('#articles/', ''))
                this.articles ('articles/1')

            page = 1
        })

        on ('items', ev => {

            page = ev.detail.current || 1

            this.item.close ()
            this.error.close ()
            this.items.show (ev.detail)

            App.cache.setItem ('articles/' + page, ev.detail)
        })

        on ('item', ev => {

            this.item.show (ev.detail)

            App.cache.setItem ('article/' + ev.detail.id, ev.detail)
        })

        new Router ([
            [/^articles(\/\d*)*$/, this.articles],
            [/^article\/\d+$/, this.article],
            [/^.*$/, this.not_found]
        ]).listen ()

        const hash = location.hash.replace ('#', '')

        if (hash === 'articles/1')
            this.update ()

        else
            Router.navigate (hash || 'articles/1')
    }

    not_found (hash) {

        emit ('error', `Not found: ${hash}`)
    }

    async article (hash) {

        let item = App.cache.getItem (hash)

        if ( ! item)

            item = await fetch ('/item/' + (hash.replace ('article/', '') || 0))
                .then (response => response.json ())
                .catch (r => emit ('error', r.error))

        if ( ! item.error)
            emit ('item', item)

        else
            emit ('error', item.error)
    }

    async articles (hash) {

        let items = App.cache.getItem (hash)

        if ( ! items)
            items = await fetch ('/page/' + (( + hash.replace ('articles/', '')) || 1))
                .then (response => response.json ())
                .catch (r => emit ('error', r.error))

        if ( ! items.error)
            emit ('items', items)

        else
            emit ('error', items.error)

        window.scrollTo (0, 0)
    }

    async update () {

        $ ('#spinner').addClass ('animate')

        emit ('update.start')

        App.cache.clear ()

        const options = {
            method: 'POST',
            body: JSON.stringify ({action: 'update'})
        }

        let page = await fetch ('/update', options)
            .then (r => r.json ())
            .catch (page => emit ('error', page.error))

        $ ('#spinner').removeClass ('animate')

        if ( ! page.error)
            emit ('update.end')

        else
            emit ('error', page.error)
    }
}

App.cache = (() => {
    let items = {}
    return {
        getItem: k => items [k] || false,
        setItem: (k, v) => items [k] = v,
        clear: () => items = {}
    }
}) ()

App.start = () => window.app = new App ()

on ('DOMContentLoaded', App.start)