# Joomla Rave Payment Forms

 - **Contributors:** [Bosun Olanrewaju](https://github.com/bosunolanrewaju), [Anjola Bassey](https://github.com/anjolabassey)
 - **Tags:** rave, payment form, payment gateway, bank account, credit card, debit card, nigeria, kenya, international, mastercard, visa, joomla
 - **Requires at least:** Joomla 2.5
 - **Tested on:** Joomla 3 (3.9.21)
 - **Stable tag:** 0.1.0
 - **License:** [GNU GPLv2 ](https://github.com/bosunolanrewaju/rave-payment-forms/blob/master/LICENSE)

Take donations and payments for services on your Joomla site using Flutterwave Checkout.



## Description


Accept Credit card, Debit card and Bank account payment directly on your Joomla site with the Flutterwave payment gateway.

#### Take donations and payments easily and directly on your site

Flutterwave is available in:__Nigeria__, __Ghana__, __Kenya__, __Uganda__, __Rwanda__, __South Africa__,__United States__ and [90+ Countries](https://support.flutterwave.com/article/162-accepted-currencies). Signup for an account [here](https://www.flutterwave.com)


## Installation


*  Download the extension zip file to your computer.
*  Login to your Administrator Dashboard.
*  Click on "Extensions > Manage > Install" from the top menu.
*  Click on the "Upload Package File" tab, then click "Choose File" to select the zip file you downloaded. Click "Upload and Install" to start the installation.

This installs a __Rave Payments Forms__ Component and a __Rave Payments Forms__ Module to your Joomla website. The Component lets you set global configuration and provides a page that lists all transactions made through Rave Checkout. And you create forms with the module.

## Configuration

Click on __Components__ > __Rave Payments Forms__ from the top menu. Then select the __Options__ button at the top left of the page.

<img width="250" alt="Configuration button" src="https://cloud.githubusercontent.com/assets/8383666/22316729/6bf3dbfc-e36f-11e6-9b65-eb590894231d.png">

On the __Rave Payments Configuration__ Page:

<img width="650" alt="Configuration page" src="https://res.cloudinary.com/flutterwavedeveloper/image/upload/v1553784704/rave-react-native/joomla_forms_config.png">


* __Test Public Key__ - (Required) Your Rave Checkout public key which can be retrieved from "Rave Checkout" page on your Rave account dashboard.
* __Test Secret Key__ - (Required) Your Rave Checkout secret key which can be retrieved from "Rave Checkout" page on your Rave account dashboard.
* __Live Public Key__ - (Required) Your Rave Checkout public key which can be retrieved from "Rave Checkout" page on your Rave account dashboard.
* __Live Secret Key__ - (Required) Your Rave Checkout secret key which can be retrieved from "Rave Checkout" page on your Rave account dashboard.
* __Go live__ - (Optional) Choose to use the live account when you are ready to go live (Must be set to Yes for Production). Default: NO.
* __Modal Title__ - (Optional) Customize the title of the Pay Modal. Default is RAVE CHECKOUT.
* __Modal Logo__ - (Optional) Customize the logo on the Pay Modal. Upload logo to media and select. Default is Rave logo.
* __Success Redirect URL__ - (Optional) The URL the user should be redirected to after a successful payment. Enter a full url (with 'http'). Default: "".
* __Failed Redirect URL__ - (Optional) The URL the user should be redirected to after a failed payment. Enter a full url (with 'http'). Default: "".
* __Charge Currency__ - (Optional) The currency the user is charged. Default: "NGN".
* __Disable module style__ - (Optional) Disables styles from the module and uses template's style instead.
* __Weekly Plan ID__ - (Optional) The ID of the weekly payment plan interval set on your Rave dashboard.
* __Monthly Plan ID__ - (Optional) The ID of the monthly payment plan interval set on your Rave dashboard.
* __Quarterly Plan ID__ - (Optional) The ID of the quarterly payment plan interval set on your Rave dashboard.
* __Annual Plan ID__ - (Optional) The ID of the annual payment plan interval set on your Rave dashboard.
* Click __Save & Close__ to save your settings.



### Styling
You can enable activated template's style to override form's default style from the __Component Configuration__ page.
Or you can override the _form_ class `.flw-simple-pay-now-form` in your stylesheet.



## Usage
<img width="650" alt="Article page" src="https://res.cloudinary.com/flutterwavedeveloper/image/upload/v1553784942/rave-react-native/jooomla_forms_usage.png">

Click on __Extensions__ > __Modules__ from the top menu. From the list of created module instances, an unpublished form was created from the installation. This can be used or you can create another form by clicking the green __New__ button at the top left of the page and selecting __Rave Payment Forms__ from the list of module types.

* __Title__ - (Required) The title of the form you want create. The title can be hidden from the view by clicking hide  button on the "Show Title" option on the right of the page.
* __Module__ tab contains form specific configuration:
  - __Amount__ The amount to charge the customer (if left blank, customer will be asked to enter amount).
  - __Use logged in user email__ Use logged-in user's email as customer email (If set to 'No', customer will be asked to enter email).
  - __Enable Recurring Payment__ If set to 'Yes', customer will be asked to choose a subscription plan.
  - __Button Text__ - (Optional) The text to display on the form button. Default: "PAY NOW".
  - Other options (on the right):
    * __Position__ Set the position (place) to display the form on your site. This is dependent on the current template position. But, the module provides an extra position named __Rave In-Article__ and can be selected from the list. This lets you display the form on an article page (more below). Also, you can set (enter) a custom position.
    * __Status__ The status of the form. Set to __Published__ to have the form available for display.
* __Menu Assignment__ tab lets you select the page(s) that will have access to the form. "No pages" means the form won't be available. Recommended is "On all pages". You can use "Only on the pages selected" to restrict the form to certain pages (pages need to exist).
* Click __Save & Close__ to create (or save) the form.


### Display form in article body

To display the form within the article body, make sure you set the position for the form to __Rave In-Article__ when creating the form (Module settings). This is a position provided by default by the module to avoid conflicts with template's positions.

Click on __Content__ > __Articles__ > __Add New Article__ and give it a title and load the provided __Rave In-Article__ position by putting this in the Content Editor:

```
{loadposition Rave In-Article}
```

This will output all the content set to display at this position (Rave In-Article) here. This comes handy if you want to have multiple forms on the same page.

To have different forms on different pages, you should set a custom position for each of the forms and loading the positions in their respective articles (pages).

```
{loadposition name_of_custom_postion}
```

Note: replace `name_of_custom_postion` with the name of the diferent positions you created.

<img width="650" alt="Article page" src="https://cloud.githubusercontent.com/assets/8383666/22317255/c2e1d4de-e372-11e6-979a-0ed773e71742.png">

Set __Status__ to _Published_ and click __Save & Close__. That's it, you have the form on the article page. Attach the article to a Menu so you can see it.


## Transaction List ##

All the payments made through the forms to Rave can be accessed on __Components__ > __Rave Payments Forms__ > __Transaction List__ page.

<img width="650" alt="Article page" src="https://cloud.githubusercontent.com/assets/8383666/22316675/1858ae8c-e36f-11e6-81fe-67679e7f70c1.png">

##
### TODO
* Form configuration to override global configuration from the component.
* Add advanced forms to include customization where user can choose the fields to add to the form.
* Multiple Rave Checkout integrations.
* More (that I can't think of ATM) - Suggestions and Feature request are highly welcome

### Suggestions / Contributions

For issues, suggestions and feature request, [click here](https://github.com/bosunolanrewaju/joomla-rave-payment-forms/issues).
To contribute, fork the repo, add your changes and modifications, then create a pull request.


### License

##### [GNU GPLv2](https://github.com/bosunolanrewaju/rave-payment-forms/blob/master/LICENSE)

Copyright (c) 2017, Olatunbosun Olanrewaju
