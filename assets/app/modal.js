const Modal = (($) => {

    const

        modal = /*HTML*/`
<%tag id="%id" class="modal %class" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header"></div>
      <div class="modal-body"></div>
      <div class="modal-footer"></div>
    </div>
  </div>
</%tag>`

    const

        selector = {
            header: '.modal-header',
            body: '.modal-body',
            footer: '.modal-footer'
        },

        event = {
            open_before: 'show.bs.modal',
            open_after: 'shown.bs.modal',
            close_before: 'hide.bs.modal',
            close_after: 'hidden.bs.modal'
        }

    class Modal {

        constructor (tag, id, _class) {

            this.id = '#' + id

            $ (new Template (modal).html ({tag, id, 'class': _class})).appendTo ('body')
        }

        header (html) {

            $ (this.id).find (selector.header).html (html)
            return this
        }

        body (html) {

            $ (this.id).find (selector.body).html (html)
            return this
        }

        footer (html) {

            $ (this.id).find (selector.footer).html (html).show ()
            return this
        }

        open () {

            $ (this.id).find ('.close').on ('click', this.close.bind (this))

            $ (this.id).modal ({
                backdrop: 'static',
                keyboard: false,
                show: true
            })

            window.dispatchEvent (new CustomEvent ('modal' + this.id + '.open', {'detail': this}))
            return this
        }

        close () {

            window.dispatchEvent (new CustomEvent ('modal' + this.id + '.close', {'detail': this}))
            $ (this.id).modal ('hide')
            return this
        }

        onopen (handler) {

            $ (this.id).on (event.show_after, ev => handler ())
            return this
        }

        onclose (handler) {

            $ (this.id).on (event.close_before, ev => handler ())
            return this
        }
    }

    return Modal

}) ($)