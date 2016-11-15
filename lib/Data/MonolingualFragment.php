<?php

/**
 * Jérémy Ferrero<br/>
 * Compilatio <br/>
 * GETALP - Laboratory of Informatics of Grenoble <br/>
 *
 * This work is licensed under a Creative Commons Attribution-ShareAlike 4.0 International License.
 * For more information, see http://creativecommons.org/licenses/by-sa/4.0/
 */
/* * *****************************************************************************************
 *                                      INCLUDES
 * **************************************************************************************** */

require_once("Fragment.php");

/**
 * @class MonolingualFragment
 * 
 * Classe représentant un fragment dans un contexte monolingue, le cas où la source et l'extrait suspect sont écrit dans la même langue.
 */
class MonolingualFragment extends Fragment {
    /*     * *****************************************************************************************
     *                                    CONSTRUCTOR
     * **************************************************************************************** */

    /**
     * Constructeur par défaut de la classe MonolingualFragment.
     */
    public function __construct() {
        parent::__construct();
    }

}
