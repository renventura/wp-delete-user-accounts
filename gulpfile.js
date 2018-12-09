// Translation related.
var text_domain = 'wp-delete-user-accounts'; // Your textdomain here.
var translationFile = 'wp-delete-user-accounts.pot'; // Name of the transalation file.
var translationDestination = './languages'; // Where to save the translation files.
var packageName = 'wp-delete-user-accounts'; // Package name.
var bugReport = 'https://renventura.com'; // Where can users report bugs.
var lastTranslator = 'Ren Ventura <rv@renventura.com>'; // Last translator Email ID.
var team = 'Ren Ventura <rv@renventura.com>'; // Team's Email ID.

// Files to watch
var projectPHPWatchFiles = './**/*.php'; // Path to all PHP files.

/**
 * Load Plugins.
 */
var gulp = require('gulp'); // Gulp of-course

// Utility related plugins.
var replace = require('gulp-replace'); // Search and replace text
var notify = require('gulp-notify'); // Sends message notification to you
var wpPot = require('gulp-wp-pot'); // For generating the .pot file.
var sort = require('gulp-sort'); // Recommended to prevent unnecessary changes in pot-file.


/**
 * WP POT Translation File Generator.
 *
 * * This task does the following:
 *     1. Gets the source of all the PHP files
 *     2. Search and replace the placeholder text for the text domain
 *     3. Sort files in stream by path or any custom sort comparator
 *     4. Applies wpPot with the variable set at the top of this file
 *     5. Generate a .pot file of i18n that can be used for l10n to build .mo file
 */
gulp.task('pot', function () {
  return gulp.src(projectPHPWatchFiles)
    .pipe( replace( /(?<="|')(tdomain)(?="|')/g, text_domain ) )
    .pipe(gulp.dest("./"))
    .pipe(sort())
    .pipe(wpPot({
      domain: text_domain,
      package: packageName,
      bugReport: bugReport,
      lastTranslator: lastTranslator,
      team: team
    }))
    .pipe(gulp.dest(translationDestination + '/' + translationFile))
    .pipe(notify({ message: 'TASK: "pot" Completed! 💯', onLast: true }))
});
