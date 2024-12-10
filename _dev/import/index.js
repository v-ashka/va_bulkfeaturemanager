/**
 * 2007-2020 PrestaShop SA and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0).
 * It is also available through the world-wide-web at this URL: https://opensource.org/licenses/AFL-3.0
 */

// Since PrestaShop 1.7.8 you can import components below directly from the window object.
// This is the recommended way to use them as you won't have to do any extra configuration or compilation.
// We keep the old way of importing them for backward compatibility.
// @see: https://devdocs.prestashop-project.org/1.7/development/components/global-components/
import Importer from '@pages/import-data/Importer';
import Uploader from "./Uploader";
const $ = window.$;

$(() => {

  // Fix for Importer.ts setter and getter in progressModal field
  Object.defineProperty(Importer.prototype, 'progressModal', {
    get() {
      return this._progressModal;
    },
    set(modal) {
      this._progressModal = modal;
    },
  });

  const importer = new Importer();
  importer._progressModal = importer.progressModal;

  const uploader = new Uploader();

  $(document).off('click', '.js-process-import');
  $(document).on('click', '.js-process-import', (e) => importHandler(e));

  $(document).on('click', '.js-abort-import', () => importer.requestCancelImport());
  $(document).on('click', '.js-close-modal', () => {
    importer.progressModal.hide()
    location.reload()
  });
  $(document).on('click', 'js-continue-import', () => importer.continueImport());
  $(document).on('change', '.js-import-file', () => {uploader.uploadFile()})
  function importHandler(e){
    e.preventDefault();

    let configuration = {};

    // Collect the configuration from the form into an array.
    $('#import-features-product-form').find(
      'input[name=skip]:checked, select[name^=type_value], #csv, #iso_lang, #entity,' +
      '#truncate, #match_ref, #regenerate, input[name=forceIDs]:checked, #sendemail,' +
      '#separator, #multiple_value_separator, #price_tin'
    ).each((index, $input) => {
      configuration[$($input).attr('name')] = $($input).val();
    });
    configuration['import_type'] = parseInt($('#import_type')[0].value)
    importer.import(
      $(e.currentTarget).data('import_url'),
      configuration
    )

  }

});
