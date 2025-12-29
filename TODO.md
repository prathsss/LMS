# TODO: Implement Admin Approval for Member Registrations

## Database Changes
- [ ] Modify setup.php to add 'status' column to users table (ENUM: 'pending', 'approved', 'rejected')

## Registration Logic
- [ ] Update AuthController.php register action to set status='pending' instead of role='member'
- [ ] Update AuthController.php login action to check status='approved' before allowing login

## Admin Interface
- [ ] Modify members.php to display pending registrations separately
- [ ] Add approve/reject buttons for pending members
- [ ] Update MemberController.php to handle approve/reject actions

## Testing
- [x] Database schema updated successfully with setup.php
- [ ] Test full flow: register -> admin approve -> login
- [ ] Test rejection flow
