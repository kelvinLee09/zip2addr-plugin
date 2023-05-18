jQuery(document).ready(function () {
  const wcCreateAccCheckbox = jQuery(
    "label.woocommerce-form__label-for-checkbox input#createaccount"
  );

  if (wcCreateAccCheckbox.length > 0) {
    const theLabel = wcCreateAccCheckbox.parents(
      "label.woocommerce-form__label-for-checkbox"
    );
    const theSpan = theLabel.find("span");
    theSpan.text("会員規約に同意して会員登録する　にしてください。");
  }
});
