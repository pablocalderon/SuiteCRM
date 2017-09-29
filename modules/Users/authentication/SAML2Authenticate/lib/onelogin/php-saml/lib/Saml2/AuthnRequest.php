<?php
/**
 *
 * SugarCRM Community Edition is a customer relationship management program developed by
 * SugarCRM, Inc. Copyright (C) 2004-2013 SugarCRM Inc.
 *
 * SuiteCRM is an extension to SugarCRM Community Edition developed by SalesAgility Ltd.
 * Copyright (C) 2011 - 2017 SalesAgility Ltd.
 *
 * This program is free software; you can redistribute it and/or modify it under
 * the terms of the GNU Affero General Public License version 3 as published by the
 * Free Software Foundation with the addition of the following permission added
 * to Section 15 as permitted in Section 7(a): FOR ANY PART OF THE COVERED WORK
 * IN WHICH THE COPYRIGHT IS OWNED BY SUGARCRM, SUGARCRM DISCLAIMS THE WARRANTY
 * OF NON INFRINGEMENT OF THIRD PARTY RIGHTS.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more
 * details.
 *
 * You should have received a copy of the GNU Affero General Public License along with
 * this program; if not, see http://www.gnu.org/licenses or write to the Free
 * Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA
 * 02110-1301 USA.
 *
 * You can contact SugarCRM, Inc. headquarters at 10050 North Wolfe Road,
 * SW2-130, Cupertino, CA 95014, USA. or at email address contact@sugarcrm.com.
 *
 * The interactive user interfaces in modified source and object code versions
 * of this program must display Appropriate Legal Notices, as required under
 * Section 5 of the GNU Affero General Public License version 3.
 *
 * In accordance with Section 7(b) of the GNU Affero General Public License version 3,
 * these Appropriate Legal Notices must retain the display of the "Powered by
 * SugarCRM" logo and "Supercharged by SuiteCRM" logo. If the display of the logos is not
 * reasonably feasible for technical reasons, the Appropriate Legal Notices must
 * display the words "Powered by SugarCRM" and "Supercharged by SuiteCRM".
 */

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

/**
 * SAML 2 Authentication Request
 *
 */
class OneLogin_Saml2_AuthnRequest
{

    /**
     * Object that represents the setting info
     * @var OneLogin_Saml2_Settings
     */
    protected $_settings;

    /**
     * SAML AuthNRequest string
     * @var string
     */
    private $_authnRequest;

    /**
     * SAML AuthNRequest ID.
     * @var string
     */
    private $_id;

    /**
     * Constructs the AuthnRequest object.
     *
     * @param OneLogin_Saml2_Settings $settings Settings
     * @param bool   $forceAuthn      When true the AuthNReuqest will set the ForceAuthn='true'
     * @param bool   $isPassive       When true the AuthNReuqest will set the Ispassive='true'
     * @param bool   $setNameIdPolicy When true the AuthNReuqest will set a nameIdPolicy
     */
    public function __construct(OneLogin_Saml2_Settings $settings, $forceAuthn = false, $isPassive = false, $setNameIdPolicy = true)
    {
        $this->_settings = $settings;

        $spData = $this->_settings->getSPData();
        $idpData = $this->_settings->getIdPData();
        $security = $this->_settings->getSecurityData();

        $id = OneLogin_Saml2_Utils::generateUniqueID();
        $issueInstant = OneLogin_Saml2_Utils::parseTime2SAML(time());

        $nameIdPolicyStr = '';
        if ($setNameIdPolicy) {
            $nameIDPolicyFormat = $spData['NameIDFormat'];
            if (isset($security['wantNameIdEncrypted']) && $security['wantNameIdEncrypted']) {
                $nameIDPolicyFormat = OneLogin_Saml2_Constants::NAMEID_ENCRYPTED;
            }

            $nameIdPolicyStr = <<<NAMEIDPOLICY
    <samlp:NameIDPolicy
        Format="{$nameIDPolicyFormat}"
        AllowCreate="true" />
NAMEIDPOLICY;
        }


        $providerNameStr = '';
        $organizationData = $settings->getOrganization();
        if (!empty($organizationData)) {
            $langs = array_keys($organizationData);
            if (in_array('en-US', $langs)) {
                $lang = 'en-US';
            } else {
                $lang = $langs[0];
            }
            if (isset($organizationData[$lang]['displayname']) && !empty($organizationData[$lang]['displayname'])) {
                $providerNameStr = <<<PROVIDERNAME
    ProviderName="{$organizationData[$lang]['displayname']}"
PROVIDERNAME;
            }
        }

        $forceAuthnStr = '';
        if ($forceAuthn) {
            $forceAuthnStr = <<<FORCEAUTHN

    ForceAuthn="true"
FORCEAUTHN;
        }

        $isPassiveStr = '';
        if ($isPassive) {
            $isPassiveStr = <<<ISPASSIVE

    IsPassive="true"
ISPASSIVE;
        }

        $requestedAuthnStr = '';
        if (isset($security['requestedAuthnContext']) && $security['requestedAuthnContext'] !== false) {

            $authnComparison = 'exact';
            if (isset($security['requestedAuthnContextComparison'])) {
                $authnComparison = $security['requestedAuthnContextComparison'];
            }

            if ($security['requestedAuthnContext'] === true) {
                $requestedAuthnStr = <<<REQUESTEDAUTHN
    <samlp:RequestedAuthnContext Comparison="$authnComparison">
        <saml:AuthnContextClassRef>urn:oasis:names:tc:SAML:2.0:ac:classes:PasswordProtectedTransport</saml:AuthnContextClassRef>
    </samlp:RequestedAuthnContext>
REQUESTEDAUTHN;
            } else {
                $requestedAuthnStr .= "    <samlp:RequestedAuthnContext Comparison=\"$authnComparison\">\n";
                foreach ($security['requestedAuthnContext'] as $contextValue) {
                    $requestedAuthnStr .= "        <saml:AuthnContextClassRef>".$contextValue."</saml:AuthnContextClassRef>\n";
                }
                $requestedAuthnStr .= '    </samlp:RequestedAuthnContext>';
            }
        }

        $request = <<<AUTHNREQUEST
<samlp:AuthnRequest
    xmlns:samlp="urn:oasis:names:tc:SAML:2.0:protocol"
    xmlns:saml="urn:oasis:names:tc:SAML:2.0:assertion"
    ID="$id"
    Version="2.0"
{$providerNameStr}{$forceAuthnStr}{$isPassiveStr}
    IssueInstant="$issueInstant"
    Destination="{$idpData['singleSignOnService']['url']}"
    ProtocolBinding="urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST"
    AssertionConsumerServiceURL="{$spData['assertionConsumerService']['url']}">
    <saml:Issuer>{$spData['entityId']}</saml:Issuer>
{$nameIdPolicyStr}
{$requestedAuthnStr}
</samlp:AuthnRequest>
AUTHNREQUEST;

        $this->_id = $id;
        $this->_authnRequest = $request;
    }

    /**
     * Returns deflated, base64 encoded, unsigned AuthnRequest.
     * 
     * @param bool|null $deflate Whether or not we should 'gzdeflate' the request body before we return it.
     */
    public function getRequest($deflate = null)
    {
        $subject = $this->_authnRequest;

        if (null === $deflate) {
            $deflate = $this->_settings->shouldCompressRequests();
        }

        if ($deflate) {
            $subject = gzdeflate($this->_authnRequest);
        }

        $base64Request = base64_encode($subject);
        return $base64Request;
    }

    /**
     * Returns the AuthNRequest ID.
     *
     * @return string
     */
    public function getId()
    {
        return $this->_id;
    }
}
