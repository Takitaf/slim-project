var gulp = require("gulp"),
    extReplace = require("gulp-ext-replace"),
    config = require("./gulpfile-config.json"),
    del = require("del"),
    rename = require("gulp-rename"),
    template = require("gulp-ejs-compile");

// Clean directories
gulp.task("clean", ["clean_styles", "clean_scripts", "clean_templates"], function (cb) {
    cb();
});

gulp.task("clean_styles", function () {
    return del(["public/css/*"]);
});
gulp.task("clean_scripts", function () {
    return del(["public/js"]);
});
gulp.task("clean_templates", function () {
    return del(["views/**/templates/*.jst"]);
});

// Build task
gulp.task("build", ["clean"], function () {
    gulp.start("scripts");
    gulp.start("styles");
    gulp.start("templates");
});

gulp.task("scripts", function () {
    gulp.src(config.app_files.js)
        .pipe(gulp.dest(config.dir.compile.js));

    gulp.src(config.vendor_files.js)
        .pipe(gulp.dest(config.dir.compile.js + "/vendor"));
});

gulp.task("styles", function () {
    gulp.src(config.app_files.css)
        .pipe(gulp.dest(config.dir.compile.css));

    gulp.src(config.vendor_files.css)
        .pipe(gulp.dest(config.dir.compile.css));

    gulp.src(config.vendor_files.fonts)
        .pipe(gulp.dest(config.dir.compile.fonts));
});

gulp.task("templates", function () {
    gulp.src("views/**/*.ejs")
        .pipe(template({
            name: ""
        }))
        .pipe(extReplace(".jst.js"))
        .pipe(rename(function (path) {
            path.dirname = path.dirname.replace(/precompiled_templates/, "templates");
            return path;
        }))
        .pipe(gulp.dest(function (file) {
            return file.base;
        }));
});

gulp.task("watch", ["build"], function () {
    gulp.watch(config.app_files.css, ["styles"]);
    gulp.watch(config.app_files.js, ["scripts"]);
    gulp.watch("views/**/precompiled_templates/*", ["templates"]);
});

// Default Task
gulp.task("default", ["build"]);
