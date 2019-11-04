
const Item = (($) => {

    const close = /*HTML*/`
<button type="button" class="close" onclick="history.back()"><span aria-hidden="true">&times;</span></button>`

    class Item extends Modal {

        constructor (id) {

            super ('div', `${id}_modal`, `modal-fullscreen`)
        }

        show (item) {

            this.header (item.title + close).body (item.content).open ()
        }
    }

    return Item
}) ($)