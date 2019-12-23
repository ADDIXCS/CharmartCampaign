from __future__ import with_statement
from fabric.api import *
from fabric.contrib.console import confirm
from fabric.api import put

env.user = "ec2-user"
env.roledefs = {
    'staging': ['52.207.202.222'],
    'production': ['54.84.145.184', '34.229.161.60']
}

def deploy():
    execute(cache_clear)
    execute(pull)
    execute(status)

def status():
    code_dir = '/var/www/campaign'
    with cd(code_dir):
        run("git status")

def pull():
    code_dir = '/var/www/campaign'
    with cd(code_dir):
        run("git pull")

def checkout(branch):
    code_dir = '/var/www/campaign'
    with cd(code_dir):
        run("git checkout " + branch)

def cache_clear():
    code_dir = '/var/www/campaign'
    with cd(code_dir):
        run("app/Console/cake cache_clear")
