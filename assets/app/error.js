
class Error extends Modal {

    constructor (id) {

        super ('div', `${id}_modal`, 'modal-fullscreen')
    }

    show (hash) {

        let [type, text] = ('' + hash).split (': ')

        console.log (this)

        this
            .header (type +`<button type="button" class="close" onclick="history.back ()"><span aria-hidden="true">&times;</span></button>`)
            .body (text)
            .open ()
    }
}