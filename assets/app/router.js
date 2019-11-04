
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
            return this
        }

        check (ev) {

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

        static navigate (hash) {

            location.href = (`${location.href.replace (/#(.*)$/, '')}#${hash}`)
        }
    }
}) ()