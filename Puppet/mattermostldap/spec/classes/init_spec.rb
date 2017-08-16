require 'spec_helper'
describe 'mattermostldap' do

  context 'with defaults for all parameters' do
    it { should contain_class('mattermostldap') }
  end
end
