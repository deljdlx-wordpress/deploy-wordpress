<?php
namespace Woof\Metadata;

class Taxonomy
{
    // sur quelle taxonomy nous ajoutons une métadata
    protected $taxonomy;

    protected $name;
    protected $label;

    public function __construct($taxonomy, $name, $label)
    {
        $this->taxonomy = $taxonomy;
        $this->name = $name;
        $this->label = $label;
    }

    public function register()
    {
        add_action($this->taxonomy . '_add_form_fields', [$this, 'displayAddForm']);
        add_action($this->taxonomy . '_edit_form_fields', [$this, 'displayEditForm'], 10, 1);


        add_action( 'created_' . $this->taxonomy, [$this, 'save'] );
        add_action( 'edited_' . $this->taxonomy, [$this, 'save'] );
    }

    public function displayAddForm( $taxonomy ) {
        echo '
            <div class="form-field">
                <label for="' . $this->name . '">' . $this->label . '</label>
                <input type="text" name="' . $this->name . '" id="' . $this->name . '" />
            </div>
        ';
    }

    public function save($taxonomyId)
    {
        $value = filter_input(INPUT_POST, $this->name);
        // DOC https://developer.wordpress.org/reference/functions/update_term_meta/
        update_term_meta(
            $taxonomyId,    // sur quelle taxonomie nous ajouton une metadata
            $this->name,    // nom de la metadata
            $value
        );
    }

    public function getValue($taxonomyId)
    {
        // récupération de la valeur de la metadata
        // DOC https://developer.wordpress.org/reference/functions/get_term_meta/
        $value = get_term_meta(
            $taxonomyId,
            $this->name,
            true    //Whether to return a single value. This parameter has no effect if $key is not specified.
        );

        return $value;
    }

    public function displayEditForm($taxonomy)
    {
        $value = $this->getValue($taxonomy->term_id);

        echo '
          <tr class="form-field form-required term-name-wrap">
            <th scope="row"><label for="' . $this->name . '">' . $this->label . '</label></th>
            <td>
              <input name="' . $this->name . '" id="' . $this->name . '" type="text" value="' .$value .'" size="40" aria-required="true">
            </td>
          </tr>
        ';
    }
}
