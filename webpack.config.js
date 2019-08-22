const Encore = require('@symfony/webpack-encore');

if (!Encore.isRuntimeEnvironmentConfigured()) {
  Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
  .setOutputPath('public/build/')
  .setPublicPath('/build')
  .addEntry('site', './assets/js/site.js')
  .splitEntryChunks()
  .enableSingleRuntimeChunk()
  .cleanupOutputBeforeBuild()
  .enableBuildNotifications()
  .enableSourceMaps(!Encore.isProduction())
  .configureBabel(() => {
  }, {
    useBuiltIns: 'usage',
    corejs: 3
  })
  .enableSassLoader()

;

if (Encore.isProduction()) {
  Encore
    .enableIntegrityHashes()
    .enableVersioning()
  ;
}

module.exports = Encore.getWebpackConfig();
