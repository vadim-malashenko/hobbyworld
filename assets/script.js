window.addEventListener ('DOMContentLoaded', () => {


    const on = addEventListener, off = removeEventListener
    const emit = (t, d) => dispatchEvent (new CustomEvent (t, {'detail': d}))


    const Router = (() => {

        let

            _routes = [],
            _current = null,
            _next = null,
            _handled = false,
            _listen = false

        return class Router {

            constructor (routes) {

                this.addRoutes (routes)
            }

            addRoutes (routes) {

                Array.isArray (routes)

                    &&

                routes.forEach (route =>

                    route.length == 2
                    && route [0].constructor.name == 'RegExp'
                    && typeof route [1] == 'function'

                    && _routes.push (route)
                )
            }

            listen () {

                _listen && this.stop ()
                this.start ()
            }

            check () {

                _next = Router.hash ()

                if (_next !== null && _current !== _next) {

                    _handled = false

                    _routes.forEach (route =>

                        ! _handled
                        && route [0].test (_next)

                        && (
                            _handled = true,
                            _current = _next,
                            route [1] (_current)
                        )
                    )
                }
            }

            start () {

                addEventListener ('popstate', this.check)
                _listen = true
            }

            stop () {

                removeEventListener ('popstate', this.check)
                _listen = false
            }

            static hash () {

                const matches = location.href.match (/#(.*)$/)

                return matches !== null

                    ? matches [1].replace (/^\/|\/$/, '')
                    : null
            }

            static go (to) {

                location.replace (`${location.href.replace (/#(.*)$/, '')}#${to}`)
            }
        }
    }) ()


    const Modal = (() => {

        const _modal_content = '.modal-content'

        let

            _element = null,
            _template = null

        return class {

            constructor (modalID, templateID) {

                _element = $ (document.getElementById (modalID))
                _template = new Template (templateID)
            }

            open (item) {

                _element.find (_modal_content).html (_template.html (item))
                _element.modal ('show')
                on ('popstate', this.close)
            }

            close () {

                off ('popstate', this.close)
                $(_element).modal ('hide')
            }

            on (event, handler) {

                _element.on (event, handler)
            }
        }
    }) ()

    const Items = (() => {

        let

            _element = null,
            _template = null

        return class {

            constructor (elementID, templateID) {

                _element = document.getElementById (elementID);
                _template = new Template (templateID)
            }

            update (items) {

                _element.innerHTML = items.reduce ((html, item) => {

                    html += _template.html (item)
                    return html
                }, '')
            }
        }
    }) ()

    const Paginator = (() => {

        let

            _baseUrl = '',
            _element = null,
            _template = null,
            _size = null

        return class {

            constructor (baseUrl, paginatorID, templateID) {

                _baseUrl = baseUrl
                _element = document.getElementById (paginatorID)
                _template = new Template (templateID)
                _size = _element.dataset.size || 5
            }

            update (pageNumber, pagesCount) {

                const items = []
                const x = _size - Math.ceil (pagesCount / 2) - 1
                const y = pagesCount - pageNumber

                if (pagesCount < _size)

                    _size = pagesCount

                if (pageNumber > 1 && (y <= _size - x))

                    items.push ({href: _baseUrl + 1, text: '&laquo;'})

                if (pageNumber > 1)

                    items.push ({href: _baseUrl + (pageNumber - 1), text: '&lsaquo;'})

                for (let i = Math.min (y - _size + 1, 0), p; i < Math.min (y + 1, pagesCount, _size); i++)

                    items.push ({href: _baseUrl + (p = pageNumber + i), text: p})

                if (pageNumber < pagesCount)

                    items.push ({href: _baseUrl + (pageNumber + 1), text: '&rsaquo;'})

                if (y >= _size)

                    items.push ({href: _baseUrl + pagesCount, text: '&raquo;'})

                _element.innerHTML = items.reduce ((html, item) => {

                    item.active = (item.text != pageNumber) ? '' : ' active'
                    html += _template.html (item)
                    return html
                }, '')
            }

            element () {

                return _element
            }
        }
    }) ()


    class Template {

        constructor (id) {

            this.template = document.getElementById (id).innerHTML.slice (5, - 5)
        }

        html (item) {

            return Object.keys (item).reduce ((html, key) => {

                html = html.replace (`%${key}`, item [key])
                return html
            }, this.template)
        }
    }


    class App {

        constructor () {

            const router = new Router ([
                [/^articles\/\d+$/, this.articles],
                [/^article\/\d+$/, this.article]
            ])

            const update = document.getElementById ('update')
            const items = new Items ('items', 'item_template')
            const paginator = new Paginator ('articles/', 'paginator', 'paginator_template')
            const modal = new Modal ('item_modal', 'modal_template')

            let page = 1

            update.addEventListener ('click', this.update)

            on ('update.start', () => {

                update.setAttribute ('disabled', true)
                update.querySelector ('span').style.display = 'inline-block'
                paginator.element ().querySelectorAll ('li').forEach (li => li.classList.add ('disabled'))
            })

            on ('update.end', () => {

                window.scrollTo (0, 0)
                update.removeAttribute ('disabled')
                update.querySelector ('span').style.display = 'none'
                paginator.element ().querySelectorAll ('li').forEach (li => li.classList.remove ('disabled'))

                Router.go ('articles/1')
            })

            on ('items', ev => {

                page = ev.detail.current || 1
                items.update (ev.detail.items || [])
                paginator.update (page, ev.detail.last || 1)
            })

            on ('item', ev => {

                modal.open (ev.detail)
            })

            modal.on ('hidden.bs.modal', ev => Router.go ('articles/' + page))
            modal.on ('shown.bs.modal', ev => ev.target.style ['padding-right'] = '0')

            router.listen ()
            this.update ()
        }

        article (hash) {

            fetch ('/item/' + hash.replace ('article/', ''))

                .then (response => response.json ())
                .then (item => emit ('item', item))
                .catch (console.error)
        }

        async articles (hash) {

            if ( ! App.cache.getItem (hash))

                await fetch ('/page/' + hash.replace ('articles/', ''))

                    .then (response => response.json ())
                    .then (page => App.cache.setItem (hash, page))
                    .catch (console.error)

            emit ('items', App.cache.getItem (hash))

            window.scrollTo (0, 0)
        }

        update () {

            emit ('update.start')

            App.cache.clear ()

            const options = {
                method: 'POST',
                body: JSON.stringify ({action: 'update'})
            }

            fetch ('/update', options)

                .then (r => r.json ())
                .then (r => emit ('update.end'))
                .catch (console.error)
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


    new App ()
})