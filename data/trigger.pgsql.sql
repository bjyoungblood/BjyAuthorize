-- Function
CREATE OR REPLACE FUNCTION create_default_role() RETURNS TRIGGER AS $trigger_new_user$
    BEGIN
        INSERT INTO user_role_linker VALUES(NEW.user_id, 'user');
        RETURN NEW;
    END;
$trigger_new_user$ LANGUAGE plpgsql;

-- Trigger
DROP TRIGGER trigger_new_user ON public.user;
CREATE TRIGGER trigger_new_user AFTER INSERT ON public.user
    FOR EACH ROW EXECUTE PROCEDURE create_default_role();
