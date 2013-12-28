class EntityPage extends Backbone.View
    className: "entity-page"

    events: {
        "click .btn-create": "create"
    }

    constructor: (options) ->
        @className += " " + @className + "-" + options.model.id

        super

    initialize: (options) ->
        @listenTo @model, "change:instance", @toggleForm

        super

    toggleForm: (entity, instance) ->
        if @form?
            @stopListening @form.model
            @form.remove()

        if instance?
            @listenTo instance, "sync", -> Cruddy.router.navigate instance.link()

            @form = new EntityForm model: instance
            @$el.append @form.render().$el
            @form.show()

        this

    create: ->
        Cruddy.router.navigate @model.link("create"), trigger: true

        this

    render: ->
        @dispose()

        @$el.html @template()

        @dataSource = @model.createDataSource()

        @dataGrid = new DataGrid
            model: @dataSource

        @filterList = new FilterList
            model: @dataSource.filter
            entity: @dataSource.entity

        @dataSource.fetch()

        @$el.append @filterList.render().el
        @$el.append @dataGrid.render().el

        this

    template: ->
        html = """
        <h1 class="page-header">
            #{ @model.get "title" }

        """

        if @model.get "can_create"
            html += """
                <button class="btn btn-default btn-create" type="button">
                    <span class="glyphicon glyphicon-plus"</span>
                </button>
            """

        html += "</h1>"

    dispose: ->
        @form.remove() if @form?
        @filterList.remove() if @filterList?
        @dataGrid.remove() if @dataGrid?
        @dataSource.stopListening() if @dataSource?

        this

    remove: ->
        @dispose()

        super