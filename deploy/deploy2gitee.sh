#!/usr/bin/env bash

mkdir build
git clone git@gitee.com:softsync/softsync.git build
cp -r site/. build
cp -r data/ build
cp -r app/ build
cd build
git config --local user.email "action@github.com"
git config --local user.name "GitHub Action"
git add .
git commit -m "🤖bot: Auto Update" -a
git push -u origin master
cd ../

# 更新 Gitee Pages
curl -X POST --header 'Content-Type: application/json;charset=UTF-8' 'https://gitee.com/api/v5/repos/softsync/softsync/pages/builds' -d '{"access_token":"${{ secrets.GITEE_ACCESS_TOKEN }}"}'