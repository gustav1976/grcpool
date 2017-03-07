module.exports = function(grunt) {

  grunt.initConfig({
//    pkg: grunt.file.readJSON('package.json'),
//    uglify: {
//      options: {
//        banner: '/*! <%= pkg.name %> <%= grunt.template.today("yyyy-mm-dd") %> */\n'
//      },
//      build: {
//        src: 'src/<%= pkg.name %>.js',
//        dest: 'build/<%= pkg.name %>.min.js'
//      }
//    }
	  
		phpunit: {
		    classes: {
		        dir: '../test'
		    },
		    options: {
		        bin: '../composer/vendor/bin/phpunit',
		        bootstrap: '../autoload.php',
		        colors: true
		    }
		},
		watch: {
			scripts: {
				files: ['../**/*.php','!../node/**','!../assets/**','!../composer/**'],
				tasks: ['phpunit'],
				options: {
					spawn: true,
				},
			},
		},  	
  });

  // Load the plugin that provides the "uglify" task.
//  grunt.loadNpmTasks('grunt-contrib-uglify');
//
//  // Default task(s).
//  grunt.registerTask('default', ['uglify']);

	
	grunt.loadNpmTasks('grunt-phpunit');
	grunt.loadNpmTasks('grunt-contrib-watch');
	
};


