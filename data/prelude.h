#define FFI_LIB "libprelude.so"

typedef enum {
        PRELUDE_CLIENT_EXIT_STATUS_SUCCESS = 0,
        PRELUDE_CLIENT_EXIT_STATUS_FAILURE = -1
} prelude_client_exit_status_t;

typedef struct prelude_client prelude_client_t;
typedef signed int prelude_error_t;
typedef struct idmef_message idmef_message_t;

int prelude_init(int *argc, char **argv);
int prelude_client_new(prelude_client_t **client, const char *profile);
void prelude_perror(prelude_error_t error, const char *fmt, ...);
int prelude_client_start(prelude_client_t *client);
void prelude_client_destroy(prelude_client_t *client, prelude_client_exit_status_t status);
int idmef_message_new(idmef_message_t **ret);
void prelude_client_send_idmef(prelude_client_t *client, idmef_message_t *msg);
void idmef_message_destroy(idmef_message_t *ptr);

int idmef_message_set_string(idmef_message_t *message, const char *path, const char *value);
int idmef_message_set_number(idmef_message_t *message, const char *path, double number);
int idmef_message_set_data(idmef_message_t *message, const char *path, const unsigned char *data, size_t size);

