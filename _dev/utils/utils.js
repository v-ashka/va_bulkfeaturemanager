/**
 * 2007-2024 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author    PrestaShop SA <contact@prestashop.com>
 *  @copyright 2007-2024 PrestaShop SA
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 *
 * Don't forget to prefix your containers with your own identifier
 * to avoid any conflicts with others containers.
 */


$(document).ready(function(){
  /*
   * Definition of 3 inputs (choiceAction, featureList and featureValue)
   * Defines DOM elements for action selection, feature list, and feature values
   */
  const choiceActionList = document.querySelector('#form_feature_method');
  // featureListId  - defined in js_feature_choice_type.html.twig
  const featureList = document.querySelector(featureListId);
  // featureValueListId -  defined in js_feature_choice_type.html.twig
  const featureValuesList = document.querySelector(featureValueListId);


  /**
   * Manages the visibility of feature list and feature values inputs
   * Shows or hides the input fields based on the provided boolean parameter
   *
   * @param {boolean} showItems - Determines whether to show or hide the input fields
   */
  const manipulateInputsView = (showItems = true) => {
    if(showItems){
      // Show feature values and feature list inputs
      featureValuesList.parentElement.parentElement.classList.remove('d-none');
      featureList.parentElement.parentElement.classList.remove('d-none');
    }else{
      // Hide feature values and feature list inputs
      featureValuesList.parentElement.parentElement.classList.add('d-none');
      featureList.parentElement.parentElement.classList.add('d-none');
    }
  }

  /**
   * Checks the selected action and adjusts the view of input fields accordingly
   * Handles different scenarios based on the selected action
   *
   * @param {HTMLSelectElement} element - The select element containing action choices
   */
  const checkSelectedAction = (element) => {
    // Get value of selected option in choiceActionList input
    const choiceActionSelectedOption = element.options[element.selectedIndex].value
    // Show all inputs (choiceAction, featureList and featureValue)
    manipulateInputsView(true)


    if(choiceActionSelectedOption === 'remove_feature'){
      // When selected option in choiceActionList, hide feature values input
      featureValuesList.parentElement.parentElement.classList.add('d-none');
    }
    if(choiceActionSelectedOption === 'delete_all'){
      // When selected option in choiceActionList is deleting all, hide all input fields (featureList, featureValue)
      manipulateInputsView(false)
      featureValuesList.parentElement.parentElement.classList.add('d-none');
    }
  }

  /**
   * Filters and manages feature values based on the selected feature
   * Shows/hides feature values and selects the first visible value
   *
   * @param {HTMLSelectElement} featureValList - Select element containing feature values
   * @param {HTMLSelectElement} featureList - Select element containing features
   * @param {boolean} firstPageLoad - Flag to determine if it's the initial page load
   */
  const loadFeatureValues = (
    featureValList,
    featureList,
    firstPageLoad = false) => {
    // Filter feature values based on selected feature
    Array.from(featureValList.options).forEach(item => {
      item.dataset.feature_id === featureList.options[featureList.selectedIndex].value ? (item.hidden = false) : (item.hidden = true)
    })

    // If not first page load, auto-select the first visible feature value
    if(!firstPageLoad){
      Array.from(featureValList.options).every(item => {
        if(item.hidden === false){
          item.selected = true;
          return false;
        }else{
          item.selected = false;
        }
        return true
      })
    }

  }
  // Initial load of feature values on page load
  loadFeatureValues(featureValuesList, featureList, true)

  // Event listener for feature list changes
  featureList.addEventListener('change', e => {
    loadFeatureValues(featureValuesList, featureList);
  });

  // Event listener for action selection changes
  if(choiceActionList != null){
    checkSelectedAction(choiceActionList)
    choiceActionList.addEventListener('change', e => {
      checkSelectedAction(choiceActionList)
    })
  }
})
