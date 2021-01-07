## CartThrob Order Loader

### Build Process

1. From the terminal, make sure you are in the project root directory
2. Update and commit the version number bump
    * Versioning follows [semver](https://semver.org/) (&lt;MAJOR&gt;.&lt;MINOR&gt;.&lt;PATCH&gt;)
        * MAJOR version when you make incompatible API changes
        * MINOR version when you add functionality in a backwards-compatible manner
        * PATCH version when you make backwards-compatible bug fixes. A PATCH number should be excluded if it equals '0'
    * Update `CARTTHROB_ORDER_LOADER_VERSION` constant in `system/user/addons/cartthrob_order_loader/addon.setup.php`
    * `$ git commit -am "Bumping version"`
3. Merge `develop` branch into `master` branch
    * `$ git checkout develop && git pull origin develop && git push origin develop`
    * `$ git checkout master && git pull origin master`
    * `$ git merge develop`
4. Tag the version in git
    * `$ git tag v<version-number>`
    * For example – `$ git tag v4.3` or `$ git tag v4.3.1`
5. Push all changes to GitHub
    * `$ git push --tags origin master`
6. Build CartThrob Order Loader ZIP
    * `$ npm run build-addon`
    * Built CartThrob Order Loader ZIP files will be added to the `./build` folder
1. Publish a release on GitHub
    * [Draft a New Release](https://github.com/CartThrob/Cart-LoadOrder/releases/new) on GitHub
    * Type the tag name from the tagging step above into the "Tag version" field
    * Repeat the tag name in the "Release title" field
    * Upload the built ZIP file by dropping it into the Dropzone uploader.
    * Publish the release

### Release Process
1. Update the CartThrob website. Follow CartThrob update process.
2. Update the ExpressionEngine website. Follow CartThrob update process.
3. Announcements. Follow CartThrob update process.
