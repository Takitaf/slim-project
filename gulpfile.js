var gulp = require("gulp"),
    extReplace = require("gulp-ext-replace"),
    config = require("./gulpfile-config.json"),
    del = require("del"),
    rename = require("gulp-rename"),
    template = require("gulp-ejs-compile");

// Clean directories
gulp.task("clean", function () {
    return del([
        "public/js/*",
        "public/css/*",
        "views/**/templates/*.jst"
    ]);
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
        .pipe(gulp.dest(config.dir.compile.css + "/vendor"));
});

gulp.task("templates", ["clean"], function () {
    gulp.src("views/**/*.ejs")
        .pipe(template({
            name: ""
        }))
        .pipe(extReplace(".jst"))
        .pipe(rename(function (path) {
            path.dirname = path.dirname.replace(/precompiled_templates/, "templates");
            return path;
        }))
        .pipe(gulp.dest(function (file) {
            return file.base;
        }));
});

gulp.task("watch", function () {
    gulp.start("build");
    gulp.watch(config.app_files.css, ["clean", "styles"]);
    gulp.watch(config.app_files.js, ["clean", "scripts"]);
    gulp.watch("views/**/precompiled_templates/*", ["clean", "templates"]);
});

// Default Task
gulp.task("default", ["build"]);
