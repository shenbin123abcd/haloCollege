'use strict';
var gulp = require('gulp');
var gulpLoadPlugins = require('gulp-load-plugins');
var plugins = gulpLoadPlugins();
var sourcemaps = require('gulp-sourcemaps');
var fs = require('fs');
var browserSync = require('browser-sync').create();
var devip = require('dev-ip');
// var merge = require('merge-stream');
// var buffer = require('vinyl-buffer');
// var tiny = require('gulp-tinypng-nokey');
//console.log(devip());
gulp.task('generateDistVersion', function () {
    var version=(new Date().getTime());
    fs.writeFileSync('app/js/appConfig.dist.js',
        `
(function(){
    "use strict";

    window.appConfig={};
    window.appConfig.debug=false;
    window.appConfig.version='1.0.0';

    if(window.appConfig.debug){
        window.appConfig.bust='?v='+(new Date().getTime());
        window.appConfig.staticUrl=window.location.protocol+'//'+window.location.hostname+':9010/app/public';
    }else{
        window.appConfig.bust='?v='+${version};
        window.appConfig.staticUrl='';
    }
}());
    `
    );
});
gulp.task('clearDistVersion',['build'], function () {
    fs.writeFileSync('app/js/appConfig.dist.js','');
});

gulp.task('sass',['images'], function () {
    return gulp.src(['app/css/*.scss'])
        .pipe(sourcemaps.init())
        .pipe(plugins.sass({outputStyle: 'compact'}))
        .pipe(plugins.autoprefixer({
            browsers:  ['> 0%'],
            cascade: false
        }))
        .pipe(sourcemaps.write())

        .pipe(gulp.dest('app/css'));
});



gulp.task('haloIcon', function () {
    return gulp.src('app/css/lib/ux_*/*iconfont.*')
        .pipe(plugins.flatten())
        .pipe(gulp.dest('../../Public/Home/Uc/css'))
});


gulp.task('images', function () {
    return gulp.src(['app/images/**/*.{jpg,png,gif,svg}'])
        // .pipe(plugins.imagemin())
        .pipe(plugins.rev())
        .pipe(gulp.dest('../../Public/Home/Uc/images'))
        .pipe(plugins.rev.manifest())
        .pipe(gulp.dest('tmp/images'))
});





gulp.task('build', ['sass','generateDistVersion','haloIcon'], function () {
    var htmlFilter = plugins.filter('**/*.html',{restore: true});
    var jsFilter = plugins.filter('**/*.js',{restore: true});
    var cssFilter = plugins.filter('**/*.css',{restore: true});
    var manifestHtml = gulp.src("tmp/images/rev-manifest.json");
    var manifestCss = gulp.src("tmp/images/rev-manifest.json");
    var manifestJs = gulp.src("tmp/images/rev-manifest.json");
    return gulp.src(['app/*.html'])
        .pipe(plugins.useref())
        .pipe(jsFilter)
        .pipe(plugins.revReplace({manifest: manifestJs}))
        .pipe(plugins.cdnizer({
            defaultCDNBase: "/Public/Home/Uc",
            // defaultCDNBase: "http://7ktq5x.com1.z0.glb.clouddn.com/Wfc2016/uc",
            allowRev: true,
            allowMin: true,
            matchers: [
                /(wScratchPad\([\s\S]*bg[\s\S]*?["'`])(.+?)(["'`].*?)/gi,
                /(wScratchPad\([\s\S]*fg[\s\S]*?["'`])(.+?)(["'`].*?)/gi,
                /(css\([\s\S]*background[\s\S]*?["'`])(.+?)(["'`].*?)/gi,
            ],
            files: [
                'images/**/*.{jpg,png,mp3,mp4}',
            ]
        }))
        .pipe(plugins.babel({
            presets: ['es2015']
        }))
        .pipe(plugins.uglify())
        .pipe(plugins.rev())
        .pipe(gulp.dest('../../Public/Home/Uc'))
        .pipe(jsFilter.restore)
        .pipe(cssFilter)
        .pipe(plugins.revReplace({manifest: manifestCss}))
        .pipe(plugins.autoprefixer({
            browsers:  ['> 0%'],
            cascade: false
        }))
        .pipe(plugins.csso())
        .pipe(plugins.rev())
        .pipe(gulp.dest('../../Public/Home/Uc'))
        .pipe(cssFilter.restore)
        .pipe(plugins.revReplace({
            replaceInExtensions: ['.js', '.css', '.html', '.ejs']
        }))
        .pipe(htmlFilter)
        .pipe(plugins.revReplace({manifest: manifestHtml}))
        .pipe(plugins.cdnizer({
            defaultCDNBase: "/Public/Home/Uc",
            // defaultCDNBase: "http://7ktq5x.com1.z0.glb.clouddn.com/Wfc2016/uc",
            allowRev: true,
            allowMin: true,
            files: [
                // Thi
                // s file is on the default CDN, and will replaced with //my.cdn.host/base/js/app.js
                'css/**/*.css',
                'js/**/*.js',
                'images/**/*.{jpg,png,mp3,mp4}',
            ]
        }))
        .pipe(plugins.htmlmin({
            removeComments: true,
            collapseWhitespace: true,
            conservativeCollapse: true,
            ignoreCustomFragments: [ /<%[\s\S]*?%>/, /<\?[\s\S]*?\?>/, /<include[\s\S]*?\/>/,/<else\/>/ ],
            minifyJS: false,
            minifyCSS: false,
        }))
        .pipe(gulp.dest('../../Application/Ke/View/Uc'))

});

gulp.task('browser-sync', function() {
    browserSync.init({
        open: false,
        ui: false,
        //notify: false,
        port: 9010,

        server: {
            baseDir: "./",
            middleware: function (req, res, next) {
                res.setHeader('Access-Control-Allow-Origin', '*');
                next();
            },
        }
    });

});





gulp.task('copy:view', function () {
    var htmlFilter = plugins.filter('**/*.html',{restore: true});
    return gulp
        .src(['app/*.html'])
        .pipe(htmlFilter)
        .pipe(plugins.cdnizer({
            defaultCDNBase: "http://"+devip()[0]+":9010/app",
            //defaultCDNBase: "../",
            allowRev: true,
            allowMin: true,
            files: [
                // Thi
                // s file is on the default CDN, and will replaced with //my.cdn.host/base/js/app.js
                'css/**/*.css',
                {
                    file: 'js/**/*.js',
                    cdn: "http://"+devip()[0]+":9010/tmp/js/${ filename }"
                },
                // 'js/**/*.js',
                'images/**/*.{jpg,png,mp3,mp4,svg}',
            ]
        }))

        // .pipe(plugins.cdnizer({
        //     defaultCDNBase: "http://"+devip()[0]+":9010/",
        //     //defaultCDNBase: "../",
        //     allowRev: true,
        //     allowMin: true,
        //     relativeRoot: 'app/public',
        //     files: [
        //         // Thi
        //         // s file is on the default CDN, and will replaced with //my.cdn.host/base/js/app.js
        //         'node_modules/**/*.css',
        //         'node_modules/**/*.js',
        //         'bower_components/**/*.css',
        //         'bower_components/**/*.js',
        //         //'public/images/**/*.{jpg,png,mp3,mp4}',
        //     ]
        // }))

        .pipe(gulp.dest('../../Application/Ke/View/Uc'))

        ;
});

gulp.task('copy:js', function () {
    return gulp
        .src(['app/js/*.js','app/js/qiniu/*.js','!app/js/app.props.js'])
        .pipe(plugins.cached('myjs'))
        .pipe(plugins.cdnizer({
            defaultCDNBase: "http://"+devip()[0]+":9010/app",
            //defaultCDNBase: "../",
            allowRev: true,
            allowMin: true,
            matchers: [
                /(wScratchPad\([\s\S]*bg[\s\S]*?["'`])(.+?)(["'`].*?)/gi,
                /(wScratchPad\([\s\S]*fg[\s\S]*?["'`])(.+?)(["'`].*?)/gi,
                /(css\([\s\S]*background[\s\S]*?["'`])(.+?)(["'`].*?)/gi,
            ],
            files: [
                'images/**/*.{jpg,png,mp3,mp4}',
            ]
        }))
        .pipe(plugins.babel({
            presets: ['es2015']
        }))
        .on('error', function(e) {
            console.error(e);
            this.emit('end');
        })
        .pipe(gulp.dest('tmp/js'))

        ;
});

gulp.task('copy:jsx', function () {
    return gulp.src(['app/js/*.jsx'])
        .pipe(plugins.cached('myreactjs:dev'))
        .pipe(plugins.cdnizer({
            defaultCDNBase: "http://"+devip()[0]+":9010/app",
            //defaultCDNBase: "../",
            allowRev: true,
            allowMin: true,
            matchers: [
                /(<img\s.*?src=\{["'`])(.+?)(["'`]\}.*?>)/gi
            ],
            files: [
                'images/**/*',
            ]
        }))
        .pipe(sourcemaps.init())
        .pipe(plugins.babel({
            presets: ['react','es2015']
        }))
        .on('error', function(e) {
            console.error(e);
            this.emit('end');
        })
        .pipe(sourcemaps.write())
        .pipe(gulp.dest('tmp/js'));
});



gulp.task('clean', require('del').bind(null, [ 
    '../../Public/Home/Uc/*',
    '../../Application/Ke/View/Uc/*',
    'tmp/*',
],{force:true}));



gulp.task('default', ['clean'], function() {
    //gulp.start('build');
    gulp.start('clearDistVersion');
});



gulp.task('dev', ['clean'], function() {
    gulp.start('watch:dev');
});



gulp.task("watch:dev", ['browser-sync','copy:view','sass','copy:js'], function(){
    gulp.watch(['app/*.html'],function(event) {
        //console.log('File ' + event.path + ' was ' + event.type + ', running tasks...');
        gulp.start('copy:view');

    });
    gulp.watch(['app/js/*.js'],function(event) {
        //console.log('File ' + event.path + ' was ' + event.type + ', running tasks...');
        gulp.start('copy:js');

    });
    gulp.watch([`app/css/**/*.scss`], ['sass']);
    

});

gulp.task('css-s', function () {
    var spriteData = gulp.src('app/images/number/*.png').pipe(plugins.spritesmith({
        imgName: 'sprite.png',
        cssName: '_sprite.scss',
        // algorithm: 'diagonal',
        imgPath  : '../images/sprite.png',
        padding  : 1,
        cssVarMap: function (sprite) {
            sprite.name = 'uc-s-' + sprite.name;
        }
    }));
    // Pipe image stream through image optimizer and onto disk

    var imgStream = spriteData.img
    // DEV: We must buffer our stream into a Buffer for `imagemin`
        .pipe(buffer())
        // .pipe(plugins.imagemin())
        //.pipe(tiny())
        .pipe(gulp.dest('app/images'));

    // Pipe CSS stream through CSS optimizer and onto disk
    var cssStream = spriteData.css
        .pipe(gulp.dest('app/css'));
    // Return a merged stream to handle both `end` events
    return merge(imgStream, cssStream);
    //return spriteData.pipe(gulp.dest('ieupdate/dist'))
});
