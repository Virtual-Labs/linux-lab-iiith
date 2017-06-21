build-dir=../build
lib-dir=lib
build-js-dir=${build-dir}/js
script-dir=../scripts

theme=blue-icon


all:  exp4 help-dir infrastructure

help-dir:
	(touch ${build-dir}/theme-${theme}; rsync -avC --exclude ${mathjax}.zip lab/* lib/* help themes/${theme}/* ${build-dir})

exp4:
	(cd lab/exp4/media; make)

infrastructure:
        (cd ${script-dir}; ./build.sh)


# don't remove the js directory, since copying it again with mathjax takes too long.
# http://superuser.com/questions/168130/unix-delete-files-and-folders-excluding-pattern
clean:
	(find ${build-dir} -mindepth 1 -maxdepth 1 ! -name js -print0 | xargs -0n1 rm -rf)









