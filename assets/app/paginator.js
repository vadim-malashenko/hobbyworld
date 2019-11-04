const Paginator = (current, last) => {

    items = []

    items.push ({id: 1, text: '&laquo;', disabled: current == 1 ? ' disabled' : ''})
    items.push ({id: (current - 1) || 1, text: '&lsaquo;', disabled: current == 1 ? ' disabled' : ''})
    items.push ({id: current, text: current, disabled: ''})
    items.push ({id: current + 1 < last ? current + 1 : last, text: '&rsaquo;', disabled: current == last ? ' disabled' : ''})
    items.push ({id: last, text: '&raquo;', disabled: current == last ? ' disabled' : ''})

    return items
}