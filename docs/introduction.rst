CMF Content Type Component
==========================

What is it?
-----------

The ContentType component allows you to map content fields to an object.
Content fields can be simple scalar values, such as text, or compound objects
such as images or geolocations.

It provides:

- **Forms**: How the content type is modified.
- **Storage**: How the content type is stored.
- **View**: How the content type is rendered.
- **Asset Dependencies**: How the content type declares its asset
  requirements.

For example, a geolocation field will require at least 3 properties to be
mapped (long, lat, zoom), and we can imagine that it requires some javascript
to show the map in the frontend and also in the backend.

In brief this will allow you to define something like:

.. code-block:: yaml

    cmf_content_type:
        mapping:
            Fqn\To\My\Entity\Page:
                title:
                    type: text
                    role: title
                body:
                    type: markdown
                    height: 500px
                    role: teaser
                image:
                    type: image
                    role: thumbnail
                location:
                    type: geolocation
                news_list:
                    type: filtered_list
                    repository: my_repository_service
                    repository_method: myMethod
                    limit: 5

And then have at your disposal:

- A symfony form for this type: including complex/compound fields (geolocation
  / news list).
- A view object: being able to render the content and / or provide access to
  services such as pagination.
- Access to asset dependencies: A list of asset dependencies (CSS / JS) can be
  generated and either mapped locally or managed by a JS package manager.
- Property role mapping: An abiltiy to retrieve specific fields for any type of content via. a
  "role" - e.g. get the title, image, or teaser for any type of content.

Getting Started
---------------

Lets start by creating a content type which will show a list of news items. It
will be made up of a ``title`` and a ``list`` of news items.

When you define a new content type you will need to create an object to which
it will be mapped, create a really simple ``NewsList`` object:

.. code-block:: php

    <?php

    class Newlist
    {
        public $title;
        public $list;
    }

Now we can define a content type for it:

.. code-block:: yaml

    cmf_content_type:
        types:
            Acme\Bundle\NewsPage:
                fields:
                    title:
                        type: Symfony\Cmf\Component\ContentType\Field\Text
                    news:
                        type: Symfony\Cmf\Component\ContentType\Field\ResourceList
                        options:
                            glob: "/cms/content/news/**/*"
                            page_size: 10
                driver: doctrine/phpcr

Above we defined ``title`` as a text field, but ``news`` is of 
the a "resource list" what this IS is not important here, what is important is
that it is a  composite type which contains multiple scalar fields of its own.

The ``Acme\Bundle\NewsPage`` will have need to be created and contain the
properties `$title` and `$field`.

Defining our content type above allows:

- The storage mapping (e.g. for Doctrine PHPCR in this example) will be
  automatically registered.
- The form type to be generated.
- The content to be transformed into a "view" object for rendering on the
  frontend.

Form Rendering
~~~~~~~~~~~~~~

Now you want to add a form to your backoffice, this as simple asking the
``content_type.form.registry`` service for your content type:

.. code-block:: php

    <?php


    // create the content object (this is just a plain PHP object)
    // TODO: What about value objects?
    $content = new NewsPage();

    $formFactory = // get the symfony form factory
    $form = $formFactory->create(NewsPage::class);

    // submit the data (bypassing validation etc..)
    $form->submit($data);
    $newsList = $form->getData();

    // persist the object
    $entityManager->persist($newsList);
    $entityManager->flush();

And it is as simple as that. Note that we pass the content type class to the
form factory and **NOT** a form type class.

Frontend (website) Rendering
~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Later, your decide that you want to render this news list on your website,
this can be achieved with a call to the ``cmf_content_type.view_builder``:

.. code-block:: php

    <?php

    $newsList = $entityManager->find('NewsList', $_GET['id']);
    $view = $viewBuilder->build('news_list', $newsList);

    echo $view['title']; // Hello World!
    echo $view['list']['collection']->count(); // number of news items
    echo $view['list']['limit']; // 5
    echo $view['list']['paginator']; // a paginator instance

So far so good, you now have a ``ContentView`` object with all the data that
you need to render your content type, but what if you are lazy and do not want
to write any HTML today?

Like the Symfony form component, it is possible to render a HTML view of the
content type:

.. code-block:: jinja

    <h1>My Website</h1>
    {{ cmf_content_type_render(view) }}

Will render something like:

.. code-block:: html

    <h1>My Website</h1>
    <div class="news_list">
        <h3>Hello World</h3>
        <div class="list">
            <div class="element">
                <h4><a href="/path/to/article">News 1</a></h4>
                <p>This is news one</p>
            </div>
            <div class="element">
                <h4><a href="/path/to/article">News 2</a></h4>
                <p>This is news two</p>
            </div>
            <div class="element">
                <h4><a href="/path/to/article">News 3</a></h4>
                <p>This is news three</p>
            </div>
        </div>
    </div>

Custom Template
~~~~~~~~~~~~~~~

Which is probably completely inappropriate for your website, so lets alter our
original content type configuration to use a custom template:

.. code-block:: yaml

    cmf_content_type:
        types:
            news_list:
                template: AppBundle:ContentType:NewsList.html.twig
                object: AppBundle\Entity\NewsList
                fields:
                    title:
                        type: string
                        required: true
                    list:
                        type: children_collection
                        defaults:
                            parent_path: /news
                            limit: 5

Storage
-------

You will make an informed choice about which storage layer you choose. Both
Doctrine ORM and Doctrine PHPCR-ODM are supported by default.

How are composite types stored? Every composite type (f.e. the
``children_collection`` above) has its own Value object (i.e. a
plain-old-PHP-object). So after submitting a form we have a data structure
such as:

.. code-block::

    NewsList {
        title => Hello World
        list => ChildrenCollectionType {
            parent_path => /cms/news
            limit => 5
        }
    }

When you choose a driver the ContentType library will automatically generate
the mapping for your chosen object (i.e. ``AppBundle\Entity\NewsList``). This
is good because it means that you do not have to do anything beyond defining
your ``NewsList`` class.

Storage Stategies
-----------------

Most databases represent a record as a key-value set, which means that storing
our complex types is not trivial.

CMSes often store content data as a serialized array, but by doing this data
integrity and searchability is sacrificed.

Doctrine ORM offers embeddables - allowing objects to be nested within a
single table, while PHPCR ODM is hierarchical and allows child objects (at a
performance cost).

The Content Type compoent aims to allow you to choose whichever solution best
fits your requirements.
