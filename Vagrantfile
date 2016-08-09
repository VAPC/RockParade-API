# vagrant plugin install vagrant-vbguest
#
# Until https://github.com/mitchellh/vagrant/issues/4968 is not closed, use:
# export ATLAS_TOKEN=`cat ~/.vagrant.d/data/vagrant_login_token`

Vagrant.configure(2) do |config|
    config.vm.box = "ubuntu/trusty64"
    config.vm.box_check_update = false
    config.vm.network "forwarded_port", guest: 80, host: 8001
    config.vm.synced_folder ".", "/vagrant", :owner => "www-data", :group => "www-data"

    config.vm.provider "virtualbox" do |vb|
        vb.gui = false
        vb.memory = "1024"
    end

    config.vm.provision "shell", inline: <<-SHELL
        export DEBIAN_FRONTEND=noninteractive
        sudo cp -R /vagrant/app/docker/etc/apt/* /etc/apt/
        sudo cp -R /vagrant/app/docker/etc/* /etc/
        sudo apt-get update
        sudo -E apt-get install -y --force-yes -o Dpkg::Options::="--force-confnew" \
            git \
            mc \
            htop \
            curl \
            nginx \
            mysql-server-5.6 \
            php7.1-cli \
            php7.1-curl \
            php7.1-fpm \
            php7.1-mysql \
            php7.1-mbstring \
            php7.1-sqlite3 \
            php7.1-intl \
            php7.1-xml
        curl -sS https://getcomposer.org/installer | php
    SHELL

    config.vm.provision "shell", run: "always", inline: <<-SHELL
        sudo bash /vagrant/app/docker/init.sh
    SHELL
end
