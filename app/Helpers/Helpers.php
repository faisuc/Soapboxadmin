<?php

if ( ! function_exists('getClientManagers'))
{

    function getClientManagers($client_id)
    {

        $client = new \App\Client;

        $managers = $client->where('client_id', $client_id)->get(['user_id'])->pluck('user_id')->toArray();

        return $managers;

    }

}

if ( ! function_exists('getUserRoles'))
{

    function getUserRoles($user_id)
    {

        $user = Sentinel::findById($user_id)->roles->pluck('slug')->toArray();

        return $user;

    }

}

if ( ! function_exists('canManageClients'))
{

    function canManageClients($user_id)
    {

        $roles = getUserRoles($user_id);
        $validRoles = ['administrator', 'account_manager'];

        if (count(array_intersect($validRoles, $roles)))
        {
            return true;
        }

        return false;

    }

}

if ( ! function_exists('canManageClient'))
{

    function canManageClient($user_id, $client_id)
    {

        $client = new \App\Client;

        $is_client = $client->where([
            ['user_id', '=', $user_id],
            ['client_id', '=', $client_id]
        ])->first();

        return $is_client ? true : false;

    }

}

if ( ! function_exists('userRoles'))
{

    function userRoles($user_id = null)
    {

        if (null != $user_id)
        {
            $roles = Sentinel::findById($user_id)->roles->pluck('slug')->toArray();
        }
        else
        {
            $roles = Sentinel::getUser()->roles()->pluck('slug')->toArray();
        }

        return $roles;

    }

}

if ( ! function_exists('is_admin'))
{

    function is_admin($user_id = null)
    {

        if (null == $user_id)
        {
            return in_array('administrator', userRoles(Sentinel::getUser()->id));
        }
        else
        {
            return in_array('administrator', userRoles($user_id));
        }

    }

}

if ( ! function_exists('is_accountManager'))
{

    function is_accountManager($user_id = null)
    {

        if (null == $user_id)
        {
            return in_array('account_manager', userRoles(Sentinel::getUser()->id));
        }
        else
        {
            return in_array('account_manager', userRoles($user_id));
        }

    }

}

if ( ! function_exists('is_client'))
{

    function is_client($user_id = null)
    {

        if (null == $user_id)
        {
            return in_array('client', userRoles(Sentinel::getUser()->id));
        }
        else
        {
            return in_array('client', userRoles($user_id));
        }

    }

}

if ( ! function_exists('convertSocialType'))
{

    function convertSocialType($type_id)
    {

        $socials = [
            1 => 'Facebook Page',
            2 => 'Facebook Group',
            3 => 'Twitter',
            4 => 'Google Business',
            5 => 'Instagram',
            6 => 'Pinterest'
        ];

        return $socials[$type_id];

    }

}