window.addEventListener ('DOMContentLoaded', () => {

    class Template {

        constructor (id) {

            this.html = document.getElementById (id).innerHTML.slice (5, - 5)
        }

        bind (item) {

            return Object.keys (item).reduce ((html, key) => {

                html = html.replace (`%${key}`, item [key])

                return html

            }, this.html)
        }
    }

    const Paginator = (() => {

        let

            _baseUrl = '',
            _paginatorElement = null,
            _itemTemplate = null,
            _size = null

        return class {

            constructor (baseUrl, paginatorElementID, itemTemplateID, size) {

                _baseUrl = baseUrl
                _paginatorElement = document.getElementById (paginatorElementID)
                _itemTemplate = new Template (itemTemplateID)
                _size = size
            }

            update (pageNumber, pagesCount) {

                const x = _size - Math.ceil (pagesCount / 2) - 1
                const y = pagesCount - pageNumber

                if (pagesCount < _size) {

                    _size = pagesCount
                }

                const items = []

                if (pageNumber > 1 && (y <= _size - x)) {

                    items.push ({
                        href: _baseUrl + 1,
                        text: '&laquo;'
                    })
                }

                if (pageNumber > 1) {

                    items.push ({
                        href: _baseUrl + (pageNumber - 1),
                        text: '&lsaquo;'
                    })
                }

                for (let i = Math.min (y - _size + 1, 0), p; i < Math.min (y + 1, pagesCount, _size); i++) {

                    items.push ({
                        href: _baseUrl + (p = pageNumber + i),
                        text: p
                    })
                }

                if (pageNumber < pagesCount) {

                    items.push ({
                        href: _baseUrl + (pageNumber + 1),
                        text: '&rsaquo;'
                    })
                }

                if (y >= _size) {

                    items.push ({
                        href: _baseUrl + pagesCount,
                        text: '&raquo;'
                    })
                }

                _paginatorElement.innerHTML = items.reduce ((html, item) => {

                    item.active = (item.text != pageNumber) ? '' : ' active'
                    html += _itemTemplate.bind (item)

                    return html
                }, '')
            }
        }
    }) ()

    const ModalWindow = (() => {

        let

            _$modalWindowElement = null,
            _modalWindowContentElement = null,
            _modalWindowContenTemplate = null

        return class {

            constructor (modalID, templateID, contentSelector) {

                const element = document.getElementById (modalID)

                _$modalWindowElement = $ (element)
                _modalWindowContentElement = element.querySelector (contentSelector)
                _modalWindowContenTemplate = new Template (templateID)

            }

            open (item) {

                _modalWindowContentElement.innerHTML = _modalWindowContenTemplate.bind (item)
                window.addEventListener ('hashchange', this.close)
                _$modalWindowElement.modal ()
            }

            close () {

                window.removeEventListener ('hashchange', this.close)
                _$modalWindowElement.modal ('hide')
            }
        }
    }) ()

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

                Array.isArray (routes) && routes.forEach (route => route.length == 2 && route [0].constructor.name == 'RegExp' && typeof route [1] == 'function' && _routes.push (route))
            }

            listen () {

                _listen && this.stop ()
                this.start ()
            }

            check () {

                _next = Router.hash ()

                if (_next !== null && _current !== _next) {

                    _handled = false

                    _routes.forEach (route => {

                        if ( ! _handled && route [0].test (_next)) {

                            _handled = true
                            _current = _next
                            route [1] (_next)
                        }
                    })
                }
            }

            stop () {

                window.removeEventListener ('popstate', this.check)
                _listen = false
            }

            start () {

                window.addEventListener ('popstate', this.check)
                _listen = true
            }

            static hash () {

                const matches = location.href.match (/#(.*)$/)

                return matches !== null
                    ? matches [1].replace (/^\/|\/$/, '')
                    : null
            }

            static go (to) {

                location.href = (`${location.href.replace (/#(.*)$/, '')}#${to}`)
            }
        }
    }) ()

    const App = (() => {

        let

            _paginator = null,
            _modalWindow = null,
            _articlesContainerElement = null,
            _shortArticleTemplate = null,
            _updateButtonElement = null


        return class {

            constructor (paginator, modalWindow, shortArticleTemplate, articlesContainerElement, updateButtonElement) {

                _paginator = paginator
                _modalWindow = modalWindow

                _articlesContainerElement = articlesContainerElement
                _shortArticleTemplate = shortArticleTemplate

                _updateButtonElement = updateButtonElement
                _updateButtonElement.addEventListener ('click', this.update)

                const router = new Router ([
                    [/^article\/\d+$/, this.article],
                    [/^articles\/\d+$/, this.articles]
                ])

                router.listen ()

                this.update ()
            }

            article (hash) {

                fetch ('/article/' + hash.replace ('article/', ''))
                    .then (response => response.json ())
                    .then (item => _modalWindow.open (item))
                    .catch (console.error)
            }

            articles (hash) {

                window.scrollTo (0, 0)

                fetch ('/articles/' + hash.replace ('articles/', ''))
                    .then (response => response.json ())
                    .then (items => {
                        _articlesContainerElement.innerHTML = items.articles.reduce ((html, item) => {
                            html += _shortArticleTemplate.bind (item)
                            return html
                        }, '')
                        _paginator.update (items.pageNumber, items.pagesCount)
                    })
                    .catch (console.error)
            }

            update () {

                fetch ('/update', {method: 'POST', body: JSON.stringify ({action: 'update'})})
                    .then (r => r.json ())
                    .then (r => {
                        window.scrollTo (0, 0)
                        Router.go ('articles/1')
                    })
                    .catch (console.error)
            }
        }
    }) ()

    new App (
        new Paginator ('articles/', 'paginatorContainer', 'paginatorItemTemplate', 2),
        new ModalWindow ('articleModalWindow', 'fullArticleTemplate', '.modal-content'),
        new Template ('shortArticleTemplate'),
        document.getElementById ('articlesContainer'),
        document.getElementById ('updateButton')
    )
})