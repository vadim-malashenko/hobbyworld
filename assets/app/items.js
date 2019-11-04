const Items = (($) => {

    const card_template = /*HTML*/`
<div class="card w-100 my-2">
  <article class="card-body">
    <header class="h6">
      <a href="%url" target="_blank">%title</a>
    </header>
    <div class="mb-2">%brief</div>
    <footer>
      <a href="#article/%id" class="btn btn-secondary btn-sm rounded-0 border-0">Читать</a>
    </footer>
  </div>
</div>`

    const close = /*HTML*/`
<button id="update" type="button" class="close btn btn-image btn-link rounded-0 border-0 mx-auto"></button>`

    const paginator_template = /*HTML*/`
<nav class="navbar mx-auto"><nav class="mx-auto"><ul id="paginator" class="pagination mb-0" data-size="1">%pages</ul></nav></nav>`

    const page_item_template = /*HTML*/`
<li class="page-item%disabled%active"><a class="page-link" href="#articles/%id">%text</a></li>`

    let _card = null, _paginator, _page_item = null

    class Items extends Modal {

        constructor (id) {

            super ('div', `${id}_modal`, `modal-fullscreen`)

            _card = new Template (card_template)
            _page_item = new Template (page_item_template)
            _paginator = new Template (paginator_template)

            this.items = {}
        }

        show (items) {

            if (items)
                this.items = items

            else
                items = this.items

            this.header (close).body ('')

            if (items.items.length > 0) {

                this.body (items.items.reduce ((html, item) => {

                    html += _card.html (item)
                    return html
                }, ''))

                const pages = Paginator (items.current, items.last).reduce ((html, item) => {

                    item.active = (item.text != items.current) ? '' : ' active'
                    html += _page_item.html (item)
                    return html
                }, '')

                this.footer (_paginator.html ({pages}))

                $ ('#paginator').show ()
            }
            else {

                $ ('#paginator').hide ()
            }

            this.open ()
        }
    }

    return Items
}) ($)