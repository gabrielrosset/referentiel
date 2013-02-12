<?php  // $Id:  print_lib_protocole.php,v 1.0 2012/02/13 00:00:00 jfruitet Exp $
///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.org                                            //
//                                                                       //
// Copyright (C) 2005 Martin Dougiamas  http://dougiamas.com             //
//                                                                       //
// This program is free software; you can redistribute it and/or modify  //
// it under the terms of the GNU General Public License as published by  //
// the Free Software Foundation; either version 2 of the License, or     //
// (at your option) any later version.                                   //
//                                                                       //
// This program is distributed in the hope that it will be useful,       //
// but WITHOUT ANY WARRANTY; without even the implied warranty of        //
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         //
// GNU General Public License for more details:                          //
//                                                                       //
//          http://www.gnu.org/copyleft/gpl.html                         //
//                                                                       //
///////////////////////////////////////////////////////////////////////////



/**************************************************************************
 * takes the current referentiel                   *
 * and mode to display                                                    *
 * input @param string  $mode                                             *
 * output null                                                            *
 **************************************************************************/
function  referentiel_edit_protocole($mode, $referentiel_instance, $select_acc=0){
global $DB;
global $CFG;
global $USER;
static $isadmin=false;
static $istutor=false;
static $isteacher=false;
static $iseditor=false;
static $referentiel_id = NULL;

	// contexte
    $cm = get_coursemodule_from_instance('referentiel', $referentiel_instance->id);
    $course = $DB->get_record('course', array('id' => $cm->course));
	if (empty($cm) or empty($course)){
        print_error('REFERENTIEL_ERROR 5 :: print_lib_protocole.php :: 46 :: You cannot call this script in that way');
	}

    // Valable pour Moodle 2.1 et Moodle 2.2
    //if ($CFG->version < 2011120100) {
        $context = get_context_instance(CONTEXT_MODULE, $cm->id);
    //} else {
        // $context = context_module::instance($cm);
    //}
	
	$referentiel_id = $referentiel_instance->ref_referentiel;
    $roles=referentiel_roles_in_instance($referentiel_instance->id);
    $iseditor=$roles->is_editor;
    $isadmin=$roles->is_admin;
    $isteacher=$roles->is_teacher;
    $istutor=$roles->is_tutor;
    $isstudent=$roles->is_student;

	/*
	// DEBUG
	if ($isadmin) echo "Admin ";
	if ($isteacher) echo "Teacher ";
	if ($istutor) echo "Tutor ";
	if ($isstudent) echo "Student ";
	*/

	if (!empty($referentiel_instance->ref_referentiel)){
		$referentiel_referentiel=referentiel_get_referentiel_referentiel($referentiel_instance->ref_referentiel);
		if (!$referentiel_referentiel){
			if ($iseditor){
				print_error(get_string('creer_referentiel','referentiel'), "edit.php?d=$referentiel_instance->id&amp;mode=editreferentiel&amp;sesskey=".sesskey());
			}
			else {
				print_error(get_string('creer_referentiel','referentiel'), "../../course/view.php?id=$course->id&amp;sesskey=".sesskey());
			}
		}

		// boite pour selectionner les utilisateurs ?
		if ($isteacher || $istutor || $iseditor){
            echo referentiel_affiche_select_protocole($referentiel_instance->id, $referentiel_referentiel->id, $course->id, $mode, $select_acc);
		}
		else{
            echo referentiel_affiche_protocole($referentiel_referentiel->id);
        }
	}
	echo '<br /><br />'."\n";
	return true;
}

/**************************************************************************
 * takes the current referentiel                   *
 * and mode to display                                                    *
 * input @param string  $mode                                             *
 * output null                                                            *
 **************************************************************************/
function  referentiel_print_protocole($mode, $referentiel_instance, $select_acc=0){
global $DB;
global $CFG;
global $USER;
static $istutor=false;
static $isteacher=false;
static $isadmin=false;
static $iseditor=false;
static $referentiel_id = NULL;

	// contexte
    $cm = get_coursemodule_from_instance('referentiel', $referentiel_instance->id);
    $course = $DB->get_record('course', array('id' => $cm->course));
	if (empty($cm) or empty($course)){
        print_error('REFERENTIEL_ERROR 5 :: print_lib_protocole.php :: 46 :: You cannot call this script in that way');
	}

    $context = get_context_instance(CONTEXT_MODULE, $cm->id);

    $roles=referentiel_roles_in_instance($referentiel_instance->id);
    $iseditor=$roles->is_editor;
    $isadmin=$roles->is_admin;
    $isteacher=$roles->is_teacher;
    $istutor=$roles->is_tutor;
    $isstudent=$roles->is_student;

	/*
	// DEBUG
	if ($isadmin) echo "Admin ";
	if ($isteacher) echo "Teacher ";
	if ($istutor) echo "Tutor ";
	if ($isstudent) echo "Student ";
	*/


	if (!empty($referentiel_instance->ref_referentiel)){
		$referentiel_referentiel=referentiel_get_referentiel_referentiel($referentiel_instance->ref_referentiel);
		if (!$referentiel_referentiel){
			if ($iseditor){
				print_error(get_string('creer_referentiel','referentiel'), "edit.php?d=$referentiel_instance->id&amp;mode=editreferentiel&amp;sesskey=".sesskey());
			}
			else {
				print_error(get_string('creer_referentiel','referentiel'), "../../course/view.php?id=$course->id&amp;sesskey=".sesskey());
			}
		}
        echo referentiel_affiche_protocole($referentiel_referentiel->id);

	}
	echo '<br /><br />'."\n";
	return true;
}


// ----------------------------------
function referentiel_affiche_select_protocole($instance_id, $refrefid, $course_id, $mode, $select_acc=0){

// tables de protocoles (seuils, item obligatoires, etc.
// MODIF JF 2012/03/26
    global $protocol_seuil_referentiel;
    global $protocol_minima_referentiel;

    global $protocol_t_domaines_oblig;
    global $protocol_t_domaines_seuil;
    global $protocol_t_domaines_minima;

    global $protocol_t_competences_oblig;
    global $protocol_t_competences_seuil;
    global $protocol_t_competences_minima;

    global $protocol_t_items_oblig;
    global $protocol_commentaire;
    global $protocol_timemodified;
    global $protocol_actif;

    global $OK_REFERENTIEL_PROTOCOLE;  // les données du protocole sont disponibles

    global $OK_REFERENTIEL_DATA;        // les données du certificat sont disponibles

    // MODIF JF 2012/03/26
    // REFERENTIEL
    global $max_minimum_referentiel;
    global $max_seuil_referentiel;

    // DOMAINES
    global $t_domaine;
    global $t_domaine_coeff;

    // COMPETENCES
    global $t_competence;
    global $t_competence_coeff;

    // ITEMS
    global $t_item_code;
    global $t_item_coeff; // coefficient poids determine par le modele de calcul (soit poids soit poids / empreinte)
    global $t_item_domaine; // index du domaine associe a un item
    global $t_item_competence; // index de la competence associee a un item
    global $t_item_poids; // poids
    global $t_item_empreinte;
    global $t_nb_item_domaine;

    // MODIF JF 2012/03/26
    // Ajout pour le protocole des minimas
    global $t_competence_domaine; // index du domaine assicie a une competence
    global $t_nb_competence_domaine; // nombre de competences par domaine
    global $t_nb_item_competence; // nombre d'item par competences

// MODIF JF 2012/06/02
    // recupere les labels des domaine, compoetence, item
    $labels=referentiel_get_labels_instance($instance_id);
    // echo "<br />DEBUG :: print_lib_protocole.php :: 197 <br />\n";
    // print_object($labels);


    $s='';
    $separateur1='/';
    $separateur2=':';

    if (!isset($OK_REFERENTIEL_DATA) || ($OK_REFERENTIEL_DATA==false) ){
			$OK_REFERENTIEL_DATA=referentiel_initialise_data_referentiel($refrefid);
	}

    if (!isset($OK_REFERENTIEL_PROTOCOLE) || ($OK_REFERENTIEL_PROTOCOLE==false) ){
	   $OK_REFERENTIEL_PROTOCOLE=referentiel_initialise_protocole_referentiel($refrefid);
	}

	if (isset($OK_REFERENTIEL_DATA) && ($OK_REFERENTIEL_DATA==true)
        && isset($OK_REFERENTIEL_PROTOCOLE) && ($OK_REFERENTIEL_PROTOCOLE==true)){
        if (!empty($protocol_t_items_oblig) && !empty($protocol_t_competences_oblig) && !empty($protocol_t_domaines_oblig)
            && !empty($protocol_t_competences_seuil) && !empty($protocol_t_domaines_seuil)){
            // DEBUG
            /*
            echo "<br />DEBUG:: lib_protocol.php :: 318 :: PROTOCOL VARIABLES<br />\n";
            echo "SEUIL : $protocol_seuil_referentiel<br />\n";
            echo "<br />DOMAINES OBLIG<br />\n";
            print_object($protocol_t_domaines_oblig);
            echo "<br />DOMAINES SEUILS<br />\n";
            print_object($protocol_t_domaines_seuil);
            echo "<br />COMPETENCES OBLIG<br />\n";
            print_object($protocol_t_competences_oblig);
            echo "<br />COMPETENCES SEUILS<br />\n";
            print_object($protocol_t_competences_seuil);
            echo "<br />ITEMS OBLIG<br />\n";
            print_object($protocol_t_items_oblig);
            */

            if (empty($protocol_commentaire)){
                $protocol_commentaire=get_string('aide_protocole_completer','referentiel');
            }

            // Mise en page du tableau
            // $nlig=count($protocol_t_items_oblig);
            $nbmaxlignes=20;
            if ($max_minimum_referentiel>=60){
                $nbmaxlignes=30;
            }


            $nlig=$max_minimum_referentiel;
            $maxlig=min($nbmaxlignes, round($nlig / 2, 0)) ;
            // echo "<br />DEBUG :: 238 :: NBITEM : $max_minimum_referentiel NBMAXLIGNES : $nbmaxlignes MAXLIG:$maxlig\n";
            $ncol= round($nlig / $maxlig, 0);

            $nligc=count($protocol_t_competences_oblig);
            $ncolc= round($nligc / $maxlig, 0);
            $ncol+=$ncolc;

            $nligd=count($protocol_t_domaines_oblig);
            $ncold= round($nligd / $maxlig, 0);
            $ncol+=$ncold;

            $s.='<div align="center">'."\n";
            $s.='<h4>'.get_string('aide_protocole','referentiel').'</h4>'."\n";

            if ($protocol_actif){
                if ($protocol_timemodified){
                    $s.= '<span class="surligne">'.get_string('protocole_active', 'referentiel').' '.get_string('depuis', 'referentiel', userdate($protocol_timemodified)).'</span>'."\n";
                }
                else{
                    $s.= '<span class="surligne">'.get_string('protocole_active', 'referentiel').'</span>'."\n";
                }
            }
            else {
                if ($protocol_timemodified){
                    $s.= '<span class="surligne">'.get_string('protocole_desactive', 'referentiel').' '.get_string('depuis', 'referentiel', userdate($protocol_timemodified)).'</span>'."\n";
                }
                else{
                    $s.= '<span class="surligne">'.get_string('protocole_desactive', 'referentiel').'</span>'."\n";
                }
            }

            $s.="\n".'<form name="form" method="post" action="edit_protocole.php?d='.$instance_id.'&amp;action=modifierprotocole&amp;mode='.$mode.'&amp;select_acc='.$select_acc.'">'."\n";
            $s.= '<table class="centree">'."\n";
                $s .='<tr>
<th>'.get_string('activation_protocole', 'referentiel').'</th></tr>'."\n";
            $s.= '<tr>
<td>';
            if ($protocol_actif){
                $s.='<input type="radio" name="protocole_actif" id="protocole_actif" value="1" checked="checked" />'.get_string('yes').' <input type="radio" name="protocole_actif" id="protocole_actif" value="0" />'.get_string('no');
            }
            else {
                $s.='<input type="radio" name="protocole_actif" id="protocole_actif" value="1" />'.get_string('yes').' <input type="radio" name="protocole_actif" id="protocole_actif" value="0" checked="checked" />'.get_string('no');
            }
            $s.= ' &nbsp;<i>('.get_string('aide_activation','referentiel').')</i>';
            $s.='</td></tr>'."\n";

            $s .='<tr>
<th>'.get_string('minima_certificat', 'referentiel').'</th></tr>';
            $s.= '<tr>
<td>
<input type="text" name="minima_certif" id="minima_certif" size="5" value="'.$protocol_minima_referentiel.'" />  / <i>'.$max_minimum_referentiel.'
&nbsp; ('.get_string('aide_minima','referentiel').')</i>
</td>
</tr>';
            $s .='<tr>
<th>'.get_string('seuil_certificat', 'referentiel').'</th></tr>';
            $s.= '<tr>
<td>
<input type="text" name="seuil_certif" id="seuil_certif" size="5" value="'.$protocol_seuil_referentiel.'" /> / <i>'.$max_seuil_referentiel.'
&nbsp; ('.get_string('aide_seuil','referentiel').')</i>';
            $s.='</td></tr>';
            $s.='<tr>
<th colspan="'.$ncol.'">'.get_string('commentaire', 'referentiel').'</th></tr>'."\n";
            $s.= '<tr>
<td colspan="'.$ncol.'">
<textarea cols="80" rows="5" name="commentaire_protocole">'.stripslashes($protocol_commentaire).'</textarea>'."\n";
            $s.='</td></tr>'."\n";

            $s.='</table>'."\n";

            $s.="<div align='center'><br />\n";
            // selection des checkbox
            $s.='<input type="button" name="select_tout_item" id="select_tout_item" value="'.get_string('select_all', 'referentiel').'"  onClick="return checkAllCheckBox()" />'."\n";
            $s.='<input type="button" name="select_aucun_items" id="select_aucun_item" value="'.get_string('select_not_any', 'referentiel').'"  onClick="return uncheckAllCheckBox()" />'."\n";
            // validation
            $s.='&nbsp; &nbsp; &nbsp; <input type="submit" value="'.get_string('savechanges').'" />'."\n";
            $s.='<input type="reset" value="'.get_string('corriger', 'referentiel').'" />'."\n";
            $s.='<input type="submit" name="cancel" value="'.get_string('quit', 'referentiel').'" />'."\n";
            $s.='</div>'."\n";

            $s.= '<table>'."\n";
            $s.='<tr valign="top"><td>'."\n";

// ITEMS
$label_i=$labels->item;
$expression=str_replace(get_string('itemo','referentiel'), $label_i, get_string('item_obligatoires','referentiel'));

            $s.='<table class="referentiel">
<tr>
<th colspan="4" class="item">'.$expression.'</th>
</tr>'."\n";

            $i=0;
            $k=0;
            $n=count($protocol_t_items_oblig);
            while ($i<$n){
                $t_oblig= explode($separateur2, $protocol_t_items_oblig[$i]);
                if (!empty($t_oblig[1])){
                    $s.='<tr><td>'.$t_oblig[0].'</td><td><input type="checkbox" name="item_oblig[]" id="item_oblig[]" value="'.$t_oblig[0].'" checked="checked" />'.'</td>'."\n";
                }
                else{
                    $s.='<tr><td>'.$t_oblig[0].'</td><td><input type="checkbox" name="item_oblig[]" id="item_oblig[]" value="'.$t_oblig[0].'" />'.'</td>'."\n";
                }
                $s.='<td><i>'.$t_item_poids[$i].'</i></td><td>'.$t_item_empreinte[$i].'</td></tr>'."\n";
                $i++;
                $k++;
                if (($k==$maxlig) && ($i<$n)){
                    $s.='</table></td><td>'."\n";
                    $s.= '<table class="referentiel">
<tr>
<th colspan="4" class="item">'.$expression.'</th>
</tr>'."\n";
                    $k=0;
                }
            }
            $s.='</table></td><td>'."\n";

// COMPETENCES
// <th colspan="2">Comp. obl.</th><th colspan="2">Min/<i>Max</i></th><th colspan="2">Seuil/<i>S(P*E)</i></th>

// MODIF JF 2012/06/02
$label_c=$labels->competence;
$expression=str_replace(get_string('compo','referentiel'), $label_c, get_string('competences_oblig_seuil','referentiel'));
$comp_th=str_replace('[','<th colspan="2">', $expression);
$comp_th=str_replace(']','</th>', $comp_th);

            $s.= '<table class="activite">
<tr>'. $comp_th.' </tr>'."\n";
            $i=0;
            $k=0;
            $n=count($protocol_t_competences_oblig);
            while ($i<$n){
                if (isset($t_competence_coeff[$i])){
                    $t_oblig= explode($separateur2, $protocol_t_competences_oblig[$i]);
                    $t_minima= explode($separateur2, $protocol_t_competences_minima[$i]);
                    $t_seuil= explode($separateur2, $protocol_t_competences_seuil[$i]);

                    $s.='<tr><td>'.$t_oblig[0].'</td><td>';

                    if (!empty($t_oblig[1])){
                        $s.='<input type="checkbox" name="comp_oblig[]" id="comp_oblig[]" value="'.$t_oblig[0].'" checked="checked" />'."\n";
                    }
                    else{
                        $s.='<input type="checkbox" name="comp_oblig[]" id="comp_oblig[]" value="'.$t_oblig[0].'" />'."\n";
                    }
                    $s.='</td>';
                    if ($t_minima[1]>0){
                        $s.='<td class="blue">';
                    }
                    else{
                        $s.='<td>';
                    }
                    $s.=' <input type="text" name="comp_minima['.$t_minima[0].']" id="comp_minima['.$t_minima[0].']" size="2" maxlength="10" value="'.$t_minima[1].'" />'."\n";
                    $s.='</td><td><i>'.$t_nb_item_competence[$i];
                    $s.='</i></td>';
                    if ($t_seuil[1]>0){
                        $s.='<td class="blue">';
                    } else {
                        $s.='<td>';
                    }
                    $s.=' <input type="text" name="comp_seuil['.$t_seuil[0].']" id="comp_seuil['.$t_seuil[0].']" size="2" maxlength="10" value="'.$t_seuil[1].'" />'."\n";
                    $s.='</td><td><i>'.$t_competence_coeff[$i];
                    $s.='</i></td></tr>'."\n";
                }
                $i++;
                $k++;
                if (($k==$maxlig) && ($i<$n)){
                    $s.='</table></td><td>'."\n";
                    $s.= '<table class="activite">
<tr>'. $comp_th.' </tr>'."\n";
                    $k=0;
                }
            }
            $s.='</table></td><td>'."\n";
// DOMAINE

// DEBUG
// echo "<br />DEBUG : print_lib_protocole :: 378\n";
// echo "<br />PROTOCOLE DOMAINE <br />\n";
// print_object($protocol_t_domaines_oblig);
// echo "<br />\n";
// print_object($protocol_t_domaines_seuil);
// echo "<br />\n";
// print_object($protocol_t_domaines_minima);
// echo "<br />EXIT\n";
// exit;
$label_d=$labels->domaine;
$expression=str_replace(get_string('domo','referentiel'), $label_d, get_string('domaines_oblig_seuil','referentiel'));
$dom_th=str_replace('[','<th colspan="2">', $expression);
$dom_th=str_replace(']','</th>', $dom_th);

            $s.= '<table class="domaine">
<tr>'. $dom_th.' </tr>'."\n";

            $i=0;
            $k=0;
            $n=count($protocol_t_domaines_oblig);
            while ($i<$n){
                if (isset($t_domaine_coeff[$i])){
                    $t_oblig= explode($separateur2, $protocol_t_domaines_oblig[$i]);

                    $t_minima= explode($separateur2, $protocol_t_domaines_minima[$i]);
                    $t_seuil= explode($separateur2, $protocol_t_domaines_seuil[$i]);

                    $s.='<tr><td>'.$t_oblig[0].'</td><td>';
                    if (!empty($t_oblig[1])){
                        $s.='<input type="checkbox" name="dom_oblig[]" id="dom_oblig[]" value="'.$t_oblig[0].'" checked="checked" />'."\n";
                    }
                    else{
                        $s.='<input type="checkbox" name="dom_oblig[]" id="dom_oblig[]" value="'.$t_oblig[0].'" />'."\n";
                    }
                    $s.='</td>';
                    if ($t_minima[1]>0){
                        $s.='<td class="green">';
                    } else {
                        $s.='<td>';
                    }
                    $s.=' <input type="text" name="dom_minima['.$t_minima[0].']" id="dom_minima['.$t_minima[0].']" size="2" maxlength="10" value="'.$t_minima[1].'" />'."\n";
                    $s.='</td><td><i>'.$t_nb_competence_domaine[$i];
                    $s.='</i></td>';
                    if ($t_seuil[1]>0){
                        $s.='<td class="green">';
                    } else {
                        $s.='<td>';
                    }
                    $s.=' <input type="text" name="dom_seuil['.$t_seuil[0].']" id="dom_seuil['.$t_seuil[0].']" size="2" maxlength="10" value="'.$t_seuil[1].'" />'."\n";
                    $s.='</td><td><i>'.$t_domaine_coeff[$i];
                    $s.='</i></td></tr>'."\n";
                }
                $i++;
                $k++;
                if (($k==$maxlig) && ($i<$n)){
                    $s.='</table></td><td>'."\n";
                    $s.= '<table class="domaine">
<tr>'. $dom_th.' </tr>'."\n";
                    $k=0;
                }
            }
            $s.='</table>
</td></tr>'."\n";
            $s.='</td></tr>';
            $s.='</table>'."\n";

            $s.="<div align='center'>\n";
            // selection des checkbox
            $s.='<input type="button" name="select_tout_item" id="select_tout_item" value="'.get_string('select_all', 'referentiel').'"  onClick="return checkAllCheckBox()" />'."\n";
            $s.='<input type="button" name="select_aucun_items" id="select_aucun_item" value="'.get_string('select_not_any', 'referentiel').'"  onClick="return uncheckAllCheckBox()" />'."\n";
            // validation
            $s.='&nbsp; &nbsp; &nbsp; <input type="submit" value="'.get_string('savechanges').'" />'."\n";
            $s.='<input type="reset" value="'.get_string('corriger', 'referentiel').'" />'."\n";
            $s.='<input type="submit" name="cancel" value="'.get_string('quit', 'referentiel').'" />'."\n";
            $s.='</div>'."\n";
            $s.='
<input type="hidden" name="action" value="modifierprotocole" />
<input type="hidden" name="select_acc" value="'.$select_acc.'" />
<!-- These hidden variables are always the same -->
<input type="hidden" name="course"        value="'.$course_id.'" />
<input type="hidden" name="sesskey"     value="'.sesskey().'" />
<input type="hidden" name="mode"          value="'.$mode.'" />'."\n";

            $s.='</form>'."\n";

            $s.='</div>'."\n";
        }
    }

    return $s;
}




// ----------------------------------
function referentiel_affiche_protocole($refrefid, $instanceid=0){

// tables de protocoles (seuils, item obligatoires, etc.

// MODIF JF 2012/06/02
    // recupere les labels des domaine, compoetence, item
    if (!empty($instanceid)){
        $labels=referentiel_get_labels_instance($instanceid);
    }
    else {
        $labels=referentiel_get_labels_occurrence($refrefid);
    }
    // echo "<br />DEBUG :: print_lib_protocole.php :: 534 <br />\n";
    // print_object($labels);

// MODIF JF 2012/03/26
    global $protocol_seuil_referentiel;
    global $protocol_minima_referentiel;

    global $protocol_t_domaines_oblig;
    global $protocol_t_domaines_seuil;
    global $protocol_t_domaines_minima;

    global $protocol_t_competences_oblig;
    global $protocol_t_competences_seuil;
    global $protocol_t_competences_minima;

    global $protocol_t_items_oblig;
    global $protocol_commentaire;
    global $protocol_timemodified;
    global $protocol_actif;

    global $OK_REFERENTIEL_PROTOCOLE;  // les données du protocole sont disponibles
    global $OK_REFERENTIEL_DATA;        // les données du certificat sont disponibles

    // MODIF JF 2012/03/26
    // REFERENTIEL
    global $max_minimum_referentiel;
    global $max_seuil_referentiel;

    // DOMAINES
    global $t_domaine;
    global $t_domaine_coeff;

    // COMPETENCES
    global $t_competence;
    global $t_competence_coeff;

    // ITEMS
    global $t_item_code;
    global $t_item_coeff; // coefficient poids determine par le modele de calcul (soit poids soit poids / empreinte)
    global $t_item_domaine; // index du domaine associe a un item
    global $t_item_competence; // index de la competence associee a un item
    global $t_item_poids; // poids
    global $t_item_empreinte;
    global $t_nb_item_domaine;

    // MODIF JF 2012/03/26
    // Ajout pour le protocole des minimas
    global $t_competence_domaine; // index du domaine assicie a une competence
    global $t_nb_competence_domaine; // nombre de competences par domaine
    global $t_nb_item_competence; // nombre d'item par competences

    // MODIF JF 2012/03/26
    global $t_competence_minima;    // INUTILE ?
    global $t_domaine_minima;       // INUTILE ?

    $s='';
    $separateur1='/';
    $separateur2=':';

    if (!isset($OK_REFERENTIEL_DATA) || ($OK_REFERENTIEL_DATA==false) ){
			$OK_REFERENTIEL_DATA=referentiel_initialise_data_referentiel($refrefid);
	}

    if (!isset($OK_REFERENTIEL_PROTOCOLE) || ($OK_REFERENTIEL_PROTOCOLE==false) ){
	   $OK_REFERENTIEL_PROTOCOLE=referentiel_initialise_protocole_referentiel($refrefid);
	}

	if (isset($OK_REFERENTIEL_DATA) && ($OK_REFERENTIEL_DATA==true)
        && isset($OK_REFERENTIEL_PROTOCOLE) && ($OK_REFERENTIEL_PROTOCOLE==true)){

        if (!empty($protocol_t_items_oblig) && !empty($protocol_t_competences_oblig) && !empty($protocol_t_domaines_oblig)
            && !empty($protocol_t_competences_seuil) && !empty($protocol_t_domaines_seuil)){
            // DEBUG
            /*
            echo "<br />DEBUG:: lib_protocol.php :: 318 :: PROTOCOL VARIABLES<br />\n";
            echo "SEUIL : $protocol_seuil_referentiel<br />\n";
            echo "<br />DOMAINES OBLIG<br />\n";
            print_object($protocol_t_domaines_oblig);
            echo "<br />DOMAINES SEUILS<br />\n";
            print_object($protocol_t_domaines_seuil);
            echo "<br />COMPETENCES OBLIG<br />\n";
            print_object($protocol_t_competences_oblig);
            echo "<br />COMPETENCES SEUILS<br />\n";
            print_object($protocol_t_competences_seuil);
            echo "<br />ITEMS OBLIG<br />\n";
            print_object($protocol_t_items_oblig);
            */

            if (empty($protocol_commentaire)){
                $protocol_commentaire=get_string('aide_protocole_completer','referentiel');
            }

            // Mise en page du tableau
            // $nlig=count($protocol_t_items_oblig);
            $nbmaxlignes=20;
            if ($max_minimum_referentiel>=60){
                $nbmaxlignes=30;
            }

            $nlig=$max_minimum_referentiel;
            $maxlig=min($nbmaxlignes, round($nlig / 2, 0)) ;
            // echo "<br />DEBUG :: 238 :: NBITEM : $max_minimum_referentiel NBMAXLIGNES : $nbmaxlignes MAXLIG:$maxlig\n";
            $ncol= round($nlig / $maxlig, 0);

            $nligc=count($protocol_t_competences_oblig);
            $ncolc= round($nligc / $maxlig, 0);
            $ncol+=$ncolc;

            $nligd=count($protocol_t_domaines_oblig);
            $ncold= round($nligd / $maxlig, 0);
            $ncol+=$ncold;


            $s.='<div align="center">'."\n";
            if ($protocol_actif){
                if ($protocol_timemodified){
                    $s.= '<span class="surligne">'.get_string('protocole_active', 'referentiel').' '.get_string('depuis', 'referentiel', userdate($protocol_timemodified)).'</span>'."\n";
                }
                else{
                    $s.= '<span class="surligne">'.get_string('protocole_active', 'referentiel').'</span>'."\n";
                }
            }
            else {
                if ($protocol_timemodified){
                    $s.= '<span class="surligne">'.get_string('protocole_desactive', 'referentiel').' '.get_string('depuis', 'referentiel', userdate($protocol_timemodified)).'</span>'."\n";
                }
                else{
                    $s.= '<span class="surligne">'.get_string('protocole_desactive', 'referentiel').'</span>'."\n";
                }
            }

            $s.= '<table class="centree">'."\n";
            $s.= '<tr valign="top">
<th>'.get_string('minima_certificat', 'referentiel').'</th></tr>'."\n";
            $s.= '<tr valign="top">
            <td>
<b>'.$protocol_minima_referentiel.'</b> / <i>'.$max_minimum_referentiel.'</i> &nbsp; &nbsp; &nbsp; ('.get_string('aide_minima','referentiel').')';
            $s.='</td></tr>';
            $s.= '<tr valign="top">
<th>'.get_string('seuil_certificat', 'referentiel').'</th>
</tr>
<tr valign="top">
<td>
<b>'.$protocol_seuil_referentiel.'</b> / <i>'.$max_seuil_referentiel.'</i> &nbsp; &nbsp; &nbsp; ('.get_string('aide_seuil','referentiel').')
</td></tr>
<tr valign="top">
<th colspan="'.$ncol.'">'.get_string('commentaire', 'referentiel').'</th></tr>'."\n";
            $s.= '<tr valign="top">
<td colspan="'.$ncol.'">
'.nl2br(stripslashes($protocol_commentaire))."\n";
            $s.='</td></tr>'."\n";

            $s.='</table>'."\n";

// ITEMS
$label_i=$labels->item;
$expression=str_replace(get_string('itemo','referentiel'), $label_i, get_string('item_obligatoires','referentiel'));

            $s.= '<table>'."\n";
            $s.='<tr valign="top"><td>'."\n";
            $s.='<table class="referentiel">
<tr valign="top">
<th colspan="4" class="item">'.$expression.'</th>
</tr>'."\n";

            $i=0;
            $k=0;
            $n=count($protocol_t_items_oblig);
            while ($i<$n){
                $t_oblig= explode($separateur2, $protocol_t_items_oblig[$i]);
                if (!empty($t_oblig[1])){
                    $s.='<tr valign="top"><td>'.$t_oblig[0].'</td><td><i>'.get_string('yes').'</i></td>'."\n";
                }
                else{
                    $s.='<tr valign="top"><td>'.$t_oblig[0].'</td><td><i>'.get_string('no').'</i></td>'."\n";
                }
                $s.='<td><i>'.$t_item_poids[$i].'</i></td><td>'.$t_item_empreinte[$i].'</td></tr>'."\n";
                $i++;
                $k++;
                if (($k==$maxlig) && ($i<$n)){
                    $s.='</table></td><td>'."\n";
                    $s.= '<table class="referentiel">
<tr valign="top">
<th colspan="4" class="item">'.$expression.'</th>
</tr>'."\n";
                    $k=0;
                }
            }
            $s.='</table></td><td>'."\n";


// COMPETENCES
// MODIF JF 2012/06/02
$label_c=$labels->competence;
$expression=str_replace(get_string('compo','referentiel'), $label_c, get_string('competences_oblig_seuil','referentiel'));
$comp_th=str_replace('[','<th colspan="2">', $expression);
$comp_th=str_replace(']','</th>', $comp_th);

            $s.= '<table class="activite">
<tr>'. $comp_th.'</tr>'."\n";

            $i=0;
            $k=0;
            $n=count($protocol_t_competences_oblig);
            while ($i<$n){
                $t_oblig= explode($separateur2, $protocol_t_competences_oblig[$i]);
                $t_minima= explode($separateur2, $protocol_t_competences_minima[$i]);
                $t_seuil= explode($separateur2, $protocol_t_competences_seuil[$i]);

                $s.='<tr valign="top"><td>'.$t_oblig[0].'</td><td>';
                if (!empty($t_oblig[1])){
                            $s.='<i>'.get_string('yes').'</i>';
                }
                else{
                            $s.='<i>'.get_string('no').'</i>';
                }
                $s.='</td>';
                if ($t_minima[1]>0){
                    $s.='<td class="blue">';
                }
                else{
                    $s.='<td>';
                }
                $s.=$t_minima[1]."\n";

                $s.='</td><td><i>'.$t_nb_item_competence[$i].'</i>';
                $s.='</td>';

                if ($t_seuil[1]>0){
                    $s.='<td class="blue">';
                } else {
                    $s.='<td>';
                }
                $s.=$t_seuil[1]."\n";
                $s.='</td><td><i>'.$t_competence_coeff[$i].'</i>';
                $s.='</td></tr>'."\n";
                $i++;
                $k++;
                if (($k==$maxlig) && ($i<$n)){
                    $s.='</table></td><td>'."\n";
                    $s.= '<table class="activite">
<tr valign="top">
<tr>'. $comp_th.'</tr>
</tr>'."\n";
                    $k=0;
                }
            }
            $s.='</table></td><td>'."\n";

// DOMAINES
$label_d=$labels->domaine;
$expression=str_replace(get_string('domo','referentiel'), $label_d, get_string('domaines_oblig_seuil','referentiel'));
$dom_th=str_replace('[','<th colspan="2">', $expression);
$dom_th=str_replace(']','</th>', $dom_th);

            $s.= '<table class="domaine">
<tr>'. $dom_th.'</tr>'."\n";
            $i=0;
            $k=0;
            $n=count($protocol_t_domaines_oblig);
            while ($i<$n){
                $t_oblig= explode($separateur2, $protocol_t_domaines_oblig[$i]);
                $t_minima= explode($separateur2, $protocol_t_domaines_minima[$i]);
                $t_seuil= explode($separateur2, $protocol_t_domaines_seuil[$i]);

                $s.='<tr valign="top"><td>'.$t_oblig[0].'</td><td>';
                if (!empty($t_oblig[1])){
                            $s.='<i>'.get_string('yes').'</i>';
                }
                else{
                            $s.='<i>'.get_string('no').'</i>';
                }
                $s.='</td>';
                if ($t_minima[1]>0){
                        $s.='<td class="green">';
                } else {
                        $s.='<td>';
                }
                $s.=$t_minima[1]."\n";

                $s.='</td><td><i>'.$t_nb_competence_domaine[$i].'</i>';

                $s.='</td>';
                if ($t_seuil[1]>0){
                        $s.='<td class="green">';
                } else {
                        $s.='<td>';
                }
                $s.=$t_seuil[1]."\n";
                $s.='</td><td><i>'.$t_domaine_coeff[$i].'</i>';
                $s.='</td></tr>'."\n";
                $i++;
                $k++;
                if (($k==$maxlig) && ($i<$n)){
                    $s.='</table></td><td>'."\n";
                    $s.= '<table class="domaine">
<tr valign="top">
<tr>'. $dom_th.'</tr>'."\n";
                    $k=0;
                }
            }
            $s.='</table>
</td></tr>'."\n";

            $s.='</table>'."\n";
            $s.='</div>'."\n";
        }
    }

    return $s;
}

?>
