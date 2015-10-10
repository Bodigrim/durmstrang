module.exports = function(grunt) {
	grunt.loadNpmTasks('grunt-contrib-watch');
	grunt.loadNpmTasks('grunt-contrib-cssmin');
	grunt.loadNpmTasks('grunt-contrib-uglify');
	grunt.loadNpmTasks('grunt-bower-concat');

	// Default task(s).
	grunt.registerTask('default', ['bower_concat', 'cssmin', 'uglify']);


	// Project configuration.
	grunt.initConfig({
		pkg: grunt.file.readJSON('package.json'),
		bower_concat: {
			all: {
				separator : ';',
				dest: 'js/bower.js',
				cssDest: 'css/bower.css'
			}
		},
		cssmin: {
			combine: {
				files: {
					'css/bundle.css': [
						//'css/bootstrap.min.css',
						//'css/bootstrap-sortable.css',
						'bower_components/bootstrap/dist/css/bootstrap.min.css',
						'bower_components/bootstrap-sortable/Contents/bootstrap-sortable.css',
						'css/style.css',
					]
				}
			}
		},
		uglify: {
			default: {
				files: {
					"js/bundle.js": [
						//"js/jquery-latest.js",
						//"js/bootstrap.min.js",
						//"js/moments.min.js",
						"js/bower.js",
						"js/main.js",
					]
				}
			}
		},
		watch:{
			A: {
				files: ["css/*.css", "!css/bundle.css"],
				tasks: ['cssmin'],
				options: {livereload: true}
			},
			B: {
				files: ["js/*.js", "!js/bundle.js"],
				tasks: ['uglify'],
				options: {livereload: true}
			},
		}
	});



};
