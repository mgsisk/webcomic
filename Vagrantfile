Vagrant.configure(2) do |config|
  host = 'local.test'
  port = '8080'
  ipv4 = '192.168.84.42'
  svtz = 'US/Michigan'
  phpv = '7.0'
  wpvn = '4.7'
  wptz = 'America/Detroit'

  config.vm.define('test') do |test|
    test.vm.provider('virtualbox') do |vb|
      vb.name = "mgsisk-webcomic-#{host[0..4]}"
    end

    test.vm.box = 'debian/contrib-jessie64'
    test.vm.hostname = host
    test.vm.network('private_network', ip: ipv4)
    test.vm.network('forwarded_port', guest: '80', host: port)
    test.vm.provision(
      :shell, path: 'vagrant.sh', args: [host, port, ipv4, svtz, phpv, wpvn, wptz]
    )
    test.hostsupdater.aliases = ["admin.#{host}"]
  end

  config.vm.define('edge') do |edge|
    edge.vm.box = 'Microsoft/EdgeOnWindows10'

    edge.vm.provider('virtualbox') do |vb|
      vb.gui = true
      vb.name = "mgsisk-webcomic-#{host[0..4]}-edge"
    end
  end
end
