class Template {

    constructor (template) {

        this.template = '' + template
    }

    html (item) {

        return item instanceof Object

            ? Object.keys (item).reduce (

                (html, key) => {

                    html = html.replace (new RegExp(`%${key}`, 'g'), item [key])
                    return html
                },
                this.template
            )
            : ''
    }
}