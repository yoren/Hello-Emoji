module.exports = function (grunt) {
	grunt.initConfig({
		
		pkg: grunt.file.readJSON('package.json'),

		cssmin: {
			options: {
				banner: '/* Hello Emoji <%= pkg.version %> - CSS */'
			},
			minify: {
				expand: true,
				cwd: 'css/',
				src: ['*.css', '!*.min.css'],
				dest: 'css/',
				ext: '.min.css'
			}
		},

		uglify: {
			options: {
				banner: '/*! Hello Emoji <%= pkg.version %> - JS */\n'
			},
			files: {
				src: 'js/hello-emoji.js',
				dest: 'js/',
				expand: true,
				flatten: true,
				ext: '.min.js'
			}
		},

		watch: {
			js:  {
				files: 'js/hello-emoji.js',
				tasks: [ 'uglify' ]
			},
			cssmin: {
				files: 'css/hello-emoji.css',
				tasks: ['cssmin']
			},
			po2mo: {
				files: 'languages/*.po',
				tasks: ['po2mo']
			},
			readme: {
				files: ['readme.txt'],
				tasks: ['wp_readme_to_markdown'],
				options: {
					spawn: false
				}
			}
		},

		// Generate .pot file
		makepot: {
			target: {
				options: {
					domainPath: '/languages',
					exclude: ['build/.*'],
					potFilename: 'hello-emoji.pot',
					processPot: function( pot ) {
						pot.headers['report-msgid-bugs-to'] = 'https://github.com/WPCollab/hello-emoji/issues\n';
						pot.headers['plural-forms'] = 'nplurals=2; plural=n != 1;';
						pot.headers['last-translator'] = 'WPCollab Team <info@wpcollab.co>\n';
						pot.headers['language-team'] = 'WPCollab Team <info@wpcollab.co>\n';
						pot.headers['x-poedit-basepath'] = '.\n';
						pot.headers['x-poedit-language'] = 'English\n';
						pot.headers['x-poedit-country'] = 'United States\n';
						pot.headers['x-poedit-sourcecharset'] = 'utf-8\n';
						pot.headers['x-poedit-keywordslist'] = '__;_e;__ngettext:1,2;_n:1,2;__ngettext_noop:1,2;_n_noop:1,2;_c,_nc:4c,1,2;_x:1,2c;_ex:1,2c;_nx:4c,1,2;_nx_noop:4c,1,2;\n';
						pot.headers['x-poedit-bookmarks'] = '\n';
						pot.headers['x-poedit-searchpath-0'] = '.\n';
						pot.headers['x-textdomain-support'] = 'yes\n';
						// Exclude string without textdomain and plugin's meta data
						var translation, delete_translation,
							excluded_strings = [ 'Settings' ],
							excluded_meta = [ 'Plugin Name of the plugin/theme', 'Author of the plugin/theme', 'Author URI of the plugin/theme' ];
						for ( translation in pot.translations[''] ) {
							delete_translation = false;
							if ( excluded_strings.indexOf( translation ) >= 0 ) {
								delete_translation = true;
								console.log( 'Excluded string: ' + translation );
							}
							if ( typeof pot.translations[''][translation].comments.extracted !== 'undefined' ) {
								if ( excluded_meta.indexOf( pot.translations[''][translation].comments.extracted ) >= 0 ) {
									delete_translation = true;
									console.log( 'Excluded meta: ' + pot.translations[''][translation].comments.extracted );
								}
							}
							if ( delete_translation ) {
								delete pot.translations[''][translation];
							}
						}
						return pot;
					},
					type: 'wp-plugin',
					updateTimestamp: true
				}
			}
		},

		// Check plugin text domain
		checktextdomain: {
			options:{
				text_domain: 'hello-emoji',
				keywords: [
					'__:1,2d',
					'_e:1,2d',
					'_x:1,2c,3d',
					'esc_html__:1,2d',
					'esc_html_e:1,2d',
					'esc_html_x:1,2c,3d',
					'esc_attr__:1,2d',
					'esc_attr_e:1,2d',
					'esc_attr_x:1,2c,3d',
					'_ex:1,2c,3d',
					'_n:1,2,4d',
					'_nx:1,2,4c,5d',
					'_n_noop:1,2,3d',
					'_nx_noop:1,2,3c,4d'
				],
				report_missing: true
			},
			files: {
				src:  [
					'**/*.php',
					'!node_modules/**',
					'!build/**'
				],
				expand: true
			}
		},

		// Generate .mo files from .po files
		potomo: {
			dist: {
				options: {
					poDel: false
				},
				files: [{
					expand: true,
					cwd: 'languages',
					src: ['*.po'],
					dest: 'languages',
					ext: '.mo',
					nonull: true
				}]
			}
		},

		// Generate README.md from readme.txt
		wp_readme_to_markdown: {
			readme: {
				files: {
					'README.md': 'readme.txt'
				},
				options: {
					screenshot_url: 'https://raw.githubusercontent.com/WPCollab/{plugin}/assets/{screenshot}.png'
				}
			}
		},

		// Check version
		checkwpversion: {
			options:{
				readme: 'readme.txt',
				plugin: 'hello-emoji.php'
			},
			plugin_vs_readme: { // Check plugin header version againts stable tag in readme
				version1: 'plugin',
				version2: 'readme',
				compare: '=='
			},
			plugin_vs_grunt: { // Check plugin header version against package.json version
				version1: 'plugin',
				version2: '<%= pkg.version %>',
				compare: '=='
			},
			plugin_vs_internal: { // Check plugin header version against internal defined version
				version1: 'plugin',
				version2: grunt.file.read('hello-emoji.php').match( /version = '(.*)'/ )[1],
				compare: '=='
			}
		},

		// Transifex integration
		exec: {
			txpull: { // Pull Transifex translation - grunt exec:txpull
				cmd: 'tx pull -a --minimum-perc=90'
			},
			txpush: { // Push pot to Transifex - grunt exec:txpush
				cmd: 'tx push -s'
			}
		}

	});

	// Load NPM tasks to be used here
	require( 'load-grunt-tasks' )( grunt );

	// register at least this one task
	grunt.registerTask('default', [
		'watch'
	]);

	grunt.registerTask( 'languages', [
		'checktextdomain',
		'makepot',
		'exec:txpush',
		'exec:txpull',
		'potomo'
	]);

	grunt.registerTask( 'readme', [
		'wp_readme_to_markdown'
	]);


};