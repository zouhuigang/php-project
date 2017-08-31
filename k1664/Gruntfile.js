module.exports = function(grunt) {

  grunt.initConfig({
    pkg: '<json:package.json>',

    clean: {
        dist: ['public/static/js', 'public/static/css']
    },

    less: {
      development: {
        options: {
          compress: false
        },
        files: {
          'public/static/css/bundle.uncompressed.css': 'less/bundle.less'
        }
      },
      production: {
        options: {
          compress: true,
          optimization: 2,
          cleancss: true
        },
        files: {
          'public/static/css/bundle.css': 'less/bundle.less'
        }
      }
    },

    watch: {
      styles: {
        files: ['less/*.less', 'less/responsive/*.less'],
        tasks: ['less'],
        options: {
          nospawn: true
        }
      },
      javascript: {
        files: ['javascript/*.js'],
        tasks: ['concat', 'uglify'],
        options: {
          nospawn: true
        }
      }
    },

    browserSync: {
      dev: {
        bsFiles: {
          src : [
          'html/*.html',
          'public/static/css/*.css',
          'public/static/js/*.js'
          ]
        },
        options: {
          watchTask: true,
          server: '.'
        }
      }
    },

    concat: {
      options: {
        separator: ';'
      },
      dist: {
        src: [
        'public/vendors/fancybox/jquery.fancybox.js',
        'public/vendors/slick/slick.js',
        'javascript/main.js'
        ],
        dest: 'public/static/js/bundle.uncompressed.js'
      }
    },

    uglify: {
      options: {
      },
      dist: {
        files: {
          'public/static/js/bundle.js': ['<%= concat.dist.dest %>']
        }
      }
    }

  });

  grunt.loadNpmTasks("grunt-contrib-clean");
  grunt.loadNpmTasks("grunt-contrib-copy");
  grunt.loadNpmTasks("grunt-contrib-less");
  grunt.loadNpmTasks("grunt-contrib-uglify");
  grunt.loadNpmTasks("grunt-contrib-concat");
  grunt.loadNpmTasks('grunt-contrib-watch');
  grunt.loadNpmTasks('grunt-text-replace');
  grunt.loadNpmTasks('grunt-browser-sync');

  grunt.registerTask('default', ['browserSync', 'less', 'concat', 'uglify', 'watch']);
  grunt.registerTask('build', ['clean', 'less', 'concat', 'uglify']);

};
