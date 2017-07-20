

module.exports = function(grunt) {

	var target = grunt.option('target') || '';
	
  grunt.initConfig({
	  
		phpunit: {
		    classes: {
		        dir: '../test/'+target
		    },
		    options: {
		        bin: '../composer/vendor/bin/phpunit',
		        bootstrap: '../test/bootstrap.php',
		        colors: true
		    }
		},
		watch: {
			scripts: {
				files: ['../**/*.php','!../node/**','!../assets/**','!../composer/**'],
				tasks: ['phpunit'],
				options: {
					atBegin: true,
					spawn: true
				},
			},
		},  	
  });


  grunt.loadNpmTasks('grunt-phpunit');
  grunt.loadNpmTasks('grunt-contrib-watch');
	
};


