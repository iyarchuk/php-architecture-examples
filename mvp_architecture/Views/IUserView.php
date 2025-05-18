<?php

namespace MVPArchitecture\Views;

/**
 * User View Interface
 * 
 * This interface defines the contract for the View component in MVP.
 * It specifies the methods that the Presenter can call on the View.
 */
interface IUserView
{
    /**
     * Display a list of users
     * 
     * @param array $users
     * @return void
     */
    public function displayUsers(array $users): void;
    
    /**
     * Display a single user
     * 
     * @param array $user
     * @return void
     */
    public function displayUser(array $user): void;
    
    /**
     * Display the user creation form
     * 
     * @return void
     */
    public function displayCreateForm(): void;
    
    /**
     * Display the user edit form
     * 
     * @param array $user
     * @return void
     */
    public function displayEditForm(array $user): void;
    
    /**
     * Display a success message
     * 
     * @param string $message
     * @return void
     */
    public function displaySuccess(string $message): void;
    
    /**
     * Display an error message
     * 
     * @param string $message
     * @return void
     */
    public function displayError(string $message): void;
    
    /**
     * Get the user ID from the view
     * 
     * @return int|null
     */
    public function getUserId(): ?int;
    
    /**
     * Get the user data from the view
     * 
     * @return array
     */
    public function getUserData(): array;
}