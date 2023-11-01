<h1>Real Estate Manager</h1>

<p>
  The Real Estate Manager module provides a system for managing and additionally
  presenting any real estates in multiple ways.
</p>
<p>
  Real Estate Manager is a base module that includes several submodules:
  <ul>
    <li>Presentation</li>
    <li>Visualization</li>
    <li>Demo</li>
  </ul>
  This module structure gives the user the possibility to install what is
  needed.
</p>
<h2>Base module</h2>
<p>
  Real Estate Manager in base version, provides related entities capable of real
  estate data storage.</br>
  The module has 4 entities:
  <ul>
    <li>Estate</li>
    <li>Building</li>
    <li>Floor</li>
    <li>Flat</li>
  </ul>
  Each of them have the ability to be fully modified through the entity types,
  fields, form and view display, publication and revision system. As mentioned
  before, content of these entities create the pyramid structure, starting from
  Estate and ending on Flat.
</p>
<p>
  <b>Example add stages without extra fields:</b>
  <ol>
    <li>
      Start by create an Estate.
      <ul>
        <li>Only a name is needed.</li>
      </ul>
    </li>
    <li>
      Then create a Building.
      <ul>
        <li>
          Beyond the name, there is the possibility to choose a parent Estate,
          this is how relation is defined.
        </li> 
        <li>
          Relation is optional, because the Building can exist alone without the
          Estate.
        </li>
        <li>
          There is a status field where the user can specify whether it is
          available, reserved or sold. It's optional because Building entities
          can but they don't have to be for sale.
        </li>
      </ul>
    </li>
    <li>
      Then create a Floor.
      <ul>
        <li>
          Beyond the name, it's required to choose a parent Building.
        </li>
        <li>
          Relation is required because the Floor can only exist in the Building.
        </li>
        <li>
          Additionally, there is an option to set the Floor as final. Thanks to
          this, the entity will not be displayed in the results looking for a
          parent entity in Flat. This works only with the module field widget.
        </li>
      </ul>
    </li>
    <li>
      Finally create a Flat.
      <ul>
        <li>
          Beyond the name, it's required to choose a parent Floor.
        </li>
        <li>
          Relation is required because Flat can only exist in the Floor.
        </li>
        <li>
          There is the same status field as in Building, but it's required
          because the Flat entities are always for sale.
        </li>
      </ul>
    </li>
  </ol>
</p>
<p>
  <b>Entity management is supported by many facilities:</b>
  <ul>
    <li>
      Field widget "Real Estate Manager Entity Reference Autocomplete", is used
      as default in each module entity. Its base functionality is to modify
      results, by displaying next to entity name its parent entity name. Also
      it's removing final floors from results.
    </li>
    <li>
      If the View module is enabled, there are prepared views with sorting,
      filtering, multiple publish and delete options.
    </li>
    <li>
      Views also adds to entities, list operation, which will display all child
      entities with all operations mentioned above.
    </li>
    <li>
      Navigation between content and types through tabs.
    </li>
    <li>
      Finally the option "Clear data" allows the user to remove all module data
      in one place, which will prepare the module to uninstall.
    </li>
  </ul>
</p>
<h2>Presentation module</h2>
<p>
  Module provides a clean block, used to present real estates in any way,
  through additional plugins. Module has a plugin manager, which gathers all
  plugins and displays them in a predetermined way. This is designed in such a
  way that external modules can create their own plugins. Block configuration
  form has tabs, each for every plugin, allowing only selected ones to be
  included. Each enabled plugin has its own settings form and generates its
  content based on them. Additionally, the order of plugins is configurable.
</p>
<p>
  Block will display two sections - the tabs with a list of enabled plugins and
  first chosen tab. Switching a tab will change the associated content by ajax
  reloading. The block must have at least one plugin enabled to be displayed.
  If only one plugin is enabled, tabs will not be displayed. The block can be
  placed repeatedly in different places on the page in any configuration.
</p>
<h2>Visualization module</h2>
<p>
  A module that provides the Presentation module plugin. The main task of the
  plugin is displaying data in an attractive visual way to the end user and
  finally contact in order to obtain a lead. It also provides a coordinate
  field to every entity type except Estate because it has no parent entity.
  Coordinates are a text field into which the attribute value should be pasted
  "d" path, previously prepared by the graphic designer, to be displayed in SVG
  embedded in the main image of the parent entity.
</p>
<p>
  <b>An example of using coordinates and the process of creating them on the
  example of Estate <- Building:</b>
  <ul>
    <li>
      The graphic designer has a photo of an Estate with Buildings.
    </li>
    <li>
      Creates an SVG with a size equal to this Estate photo.
    </li>
    <li>
      Creates outlines (path), one per building.
    </li>
    <li>
      Exports the image in SVG format.
    </li>
    <li>
      Developer opens the SVG and downloads a photo of the Estate from it.
    </li>
    <li>
      Creates the Estate entity with the above photo.
    </li>
    <li>
      Reopens the SVG and retrieves the "d" attribute values from the paths.
    </li>
    <li>
      Creates Building entities by adding their main photos (also provided by
      graphics) and the "d" attribute values are placed in the coordinates
      field.
    </li>
  </ul>
</p>
<p>
  After such a procedure, displaying the created Estate, the end user will be
  able to click on the building and go through it to its display stage. This
  action must be repeated on all entities.
</p>
<p>
  <b>Plugin available settings</b>:
  <ol>
    <li>
      The first setting is to decide what type of entity the block should start
      displaying from Estate or Building?
    </li>
    <li>
      Then select a given entity depending on the selection in the first point.
    </li>
    <li>
      Decide whether Flats or Buildings are for sale.
    </li>
    <li>
      Select the previously created form from the Webform module for generating
      leads.
    </li>
    <li>
      Optionally, the user can set the style of the main images of the entity,
      useful when the user wants e.g. format image to webp.
    </li>
    <li>
      Optionally, the user selects the color and/or target transparency of the
      displayed SVG paths after hovering over them.
    </li>
  </ol>
</p>
<p>
  The result will be displayed in the block.
</p>
<p>
  <b>The settings example (with demo module):</b>
  <ul>
    <li>
      View from estate
    </li>
    <li>
      Selected "Paradise" Estate
    </li>
    <li>
      Flats for sale
    </li>
    <li>
      "Ask for an offer" form
    </li>
    <li>
      Fill color "000000"
    </li>
    <li>
      Target transparency "60"
    </li>
  </ul>
</p>
<p>
  After locating the block on the website, the user will see the main photo of
  the Paradise Estate with descriptions and the ability to choose one of 4
  buildings. Hover color and transparency are #000 / 60%. After selecting a
  given Building, the user can select one of its Floors and then one of the
  available Flats marked with appropriate colors as described in the legend.
  After hovering over the flat, its short description will appear (it is
  possible for editing in the Flat display - tooltip). The user cannot select
  sold Flats. After selecting one of the available flats, a Flat description
  appears on the left (Flat display - description), its main photo in the
  middle, and on the right a form for generating leads, in this case the demo
  form only saves the application in the database, for demonstration purposes.
  The "Display from the Building" option, allows the user to skip displaying the
  Estate, and the "Sale buildings" option, adds an ask for an offer button at
  the Building stage and after clicking it, loads ajax modal window with a form.
  It also sets the final stage to the Floor.
</p>
<h2>Demo module</h2>
<p>
  Once installed, the module provides demo data for Real Estate module,
  specifically:
  <ul>
    <li>
     1 Estate
    </li>
    <li>
      5 Buildings
      <ul>
        <li>
          4 related to the Estate
        </li>
        <li>
          1 solo Building
        </li>
      </ul>
    </li>
    <li>
      34 Floors
      <ul>
        <li>
          32 related to Estate Buildings
        </li>
        <li>
          2 related to solo Building
        </li>
      </ul>
    </li>
    <li>
      128 Flats
      <ul>
        <li>
          4 per Floor of Buildings related to the Estate
        </li>
      </ul>
    </li>
  </ul>
  <p>
    10 imperfect images generated by AI were used to create the entities. The
    Visualization module has an additional coordinate field and if it is already
    enabled or it is enabled later, the Demo module fills these fields with the
    appropriate data. The user can use the data by using the Presentation
    modules block to display it or for presentation purposes.
  </p>
</p>
<h2>Feature of module</h2>
<p>
  Plan for the future of this module includes at least two more presentation
  plugins.
  <ul>
    <li>
      First, for search engine based on integer entities fields, displayed by
      jQuery UI Sliders, presenting real estates as tiles.
    </li>
    <li>
      Second, for display a map of the vicinity area, with configurable points
      to set such as restaurants, shops etc. based on Leaflet library.
    </li>
  </ul>
  If you want to help me with this, please contact me.
<p>
