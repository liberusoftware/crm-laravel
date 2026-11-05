# Roles and permission builder

Team owners and team administrators manage **Team → Custom roles**. Choose **Create**, enter a role name and optionally select **Sales**, **Support**, **Social media** or **Advertising**. Presets choose relevant View, Create and Edit actions and omit deletion. They are starting points, not additional system roles.

The permission builder groups readable action labels by feature, supports search and select-all, and shows the number of selected actions and deletion grants. Choosing a preset replaces the current selection on either a new or existing role. Review the result before saving; saving an existing role changes access for all members assigned that role.

Assign the role under **Team → Team roles**, individually or to a selected group. Replacing a role removes previous role assignments in that team. It leaves assignments in other teams and global platform assignments intact. Owners cannot change their own role or remove themselves through these actions. Reassign members before deleting a role that is in use.

## Presets

| Preset | Features |
| --- | --- |
| Sales | Contacts, companies, leads, deals, opportunities, tasks and notes |
| Support | Contacts, companies, messages, tasks, notes and knowledge articles |
| Social media | Social posts, marketing campaigns and tasks |
| Advertising | Advertising accounts, campaigns, ad sets, ads, marketing campaigns and dashboard widgets |

Only installed catalog permissions appear. Existing record policies, owner/territory restrictions, module availability and tenant boundaries still apply. These role definitions do not grant access to another team's data or override a policy.

## Platform administration

Super administrators use **Security & access → Global roles** in the administration panel. These definitions affect every team using the role. The screen is inaccessible to team owners and team administrators. System role names are immutable; system roles cannot be deleted, and the super-administrator role is protected from editing.

Team roles can only use the reviewed business-permission catalog. They cannot grant platform/user/role administration, security configuration, integration credentials or audit-log access. Permission names outside the catalog are rejected even if submitted directly. Reserved system names are rejected case-insensitively.

Invitation acceptance and SSO/SAML group mapping retain their verified identity-provisioning flows; interactive administration still requires a current-team owner or administrator.

Creation, updates and deletion write attributable audit records in the same transaction. Team events carry the team's scope; platform events retain a null global scope. Permission caches are invalidated after successful changes.

The built-in role matrix in `config/permissions.php` remains the seed default. Running the role seeder reapplies that matrix to system roles; custom team roles are independent of it.
