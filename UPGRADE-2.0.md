### UPGRADE FROM 1.X TO 2.0

1. The way of customizing resource definition has been changed.

   Before:

    ```yaml
        sylius_resource:
            resources:
                sylius_refund.sample_resource:
                    ...
    ```  

   After:

    ```yaml
        sylius_refund:
            resources:
                sample_resource:
                    ...
    ```

2. `_javascript.html.twig` file has been removed, and its code has been moved to `src/Resources/assets/js/refund-button.js`. When upgrading to 2.0, import the `src/Resources/assets/entrypoint.js` file into your application’s main js file.
