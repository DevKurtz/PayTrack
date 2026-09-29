CREATE TYPE "email_status" AS ENUM('sent', 'failed');--> statement-breakpoint
CREATE TYPE "email_type" AS ENUM('account_created', 'accounting_account_created', 'new_student_accounting_alert', 'tuition_assessed', 'payment_confirmation', 'payment_reminder', 'other');--> statement-breakpoint
CREATE TYPE "payment_method" AS ENUM('cash', 'gcash', 'maya', 'bank_transfer', 'online', 'card', 'other');--> statement-breakpoint
CREATE TYPE "tuition_status" AS ENUM('unpaid', 'partial', 'paid');--> statement-breakpoint
CREATE TYPE "user_role" AS ENUM('admin', 'accounting', 'student');--> statement-breakpoint
CREATE TYPE "user_status" AS ENUM('active', 'inactive', 'suspended');--> statement-breakpoint
CREATE TABLE "email_logs" (
	"id" serial PRIMARY KEY,
	"recipient_email" varchar(150) NOT NULL,
	"subject" varchar(255) NOT NULL,
	"type" "email_type" NOT NULL,
	"related_id" integer,
	"status" "email_status" DEFAULT 'sent'::"email_status" NOT NULL,
	"error_message" text,
	"sent_at" timestamp DEFAULT now() NOT NULL
);
--> statement-breakpoint
CREATE TABLE "fee_categories" (
	"id" serial PRIMARY KEY,
	"name" varchar(100) NOT NULL,
	"code" varchar(50) NOT NULL UNIQUE,
	"default_amount" numeric(12,2) DEFAULT '0.00' NOT NULL,
	"is_variable" boolean DEFAULT false NOT NULL,
	"sort_order" integer DEFAULT 99 NOT NULL,
	"is_active" boolean DEFAULT true NOT NULL,
	"created_at" timestamp DEFAULT now() NOT NULL
);
--> statement-breakpoint
CREATE TABLE "payments" (
	"id" serial PRIMARY KEY,
	"tuition_fee_id" integer NOT NULL,
	"student_id" integer NOT NULL,
	"or_number" varchar(50) NOT NULL UNIQUE,
	"amount" numeric(12,2) NOT NULL,
	"payment_method" "payment_method" DEFAULT 'online'::"payment_method" NOT NULL,
	"notes" text,
	"paid_at" timestamp DEFAULT now() NOT NULL,
	"created_at" timestamp DEFAULT now() NOT NULL
);
--> statement-breakpoint
CREATE TABLE "students" (
	"id" serial PRIMARY KEY,
	"user_id" integer NOT NULL,
	"student_id" varchar(30) NOT NULL UNIQUE,
	"first_name" varchar(80) NOT NULL,
	"last_name" varchar(80) NOT NULL,
	"middle_name" varchar(80),
	"email" varchar(150) NOT NULL,
	"grade_level" varchar(50),
	"school_year" varchar(20),
	"parent_name" varchar(160),
	"parent_email" varchar(150),
	"contact_number" varchar(20),
	"address" text,
	"created_at" timestamp DEFAULT now() NOT NULL,
	"updated_at" timestamp DEFAULT now() NOT NULL
);
--> statement-breakpoint
CREATE TABLE "tuition_fee_items" (
	"id" serial PRIMARY KEY,
	"tuition_fee_id" integer NOT NULL,
	"fee_category_id" integer NOT NULL,
	"category_name" varchar(100) NOT NULL,
	"amount" numeric(12,2) DEFAULT '0.00' NOT NULL
);
--> statement-breakpoint
CREATE TABLE "tuition_fees" (
	"id" serial PRIMARY KEY,
	"student_id" integer NOT NULL,
	"school_year" varchar(20) NOT NULL,
	"semester" varchar(30) DEFAULT '1st Semester' NOT NULL,
	"description" varchar(200) NOT NULL,
	"total_amount" numeric(12,2) DEFAULT '0.00' NOT NULL,
	"amount_paid" numeric(12,2) DEFAULT '0.00' NOT NULL,
	"due_date" date,
	"status" "tuition_status" DEFAULT 'unpaid'::"tuition_status" NOT NULL,
	"created_at" timestamp DEFAULT now() NOT NULL,
	"updated_at" timestamp DEFAULT now() NOT NULL
);
--> statement-breakpoint
CREATE TABLE "users" (
	"id" serial PRIMARY KEY,
	"username" varchar(100) NOT NULL UNIQUE,
	"name" varchar(100),
	"email" varchar(150),
	"password_hash" varchar(255) NOT NULL,
	"role" "user_role" DEFAULT 'student'::"user_role" NOT NULL,
	"is_first_login" boolean DEFAULT true NOT NULL,
	"status" "user_status" DEFAULT 'active'::"user_status" NOT NULL,
	"last_login_at" timestamp,
	"last_active_at" timestamp,
	"created_at" timestamp DEFAULT now() NOT NULL,
	"updated_at" timestamp DEFAULT now() NOT NULL
);
--> statement-breakpoint
CREATE INDEX "idx_payments_fee_id" ON "payments" ("tuition_fee_id");--> statement-breakpoint
CREATE INDEX "idx_payments_student_id" ON "payments" ("student_id");--> statement-breakpoint
CREATE INDEX "idx_students_user_id" ON "students" ("user_id");--> statement-breakpoint
CREATE INDEX "idx_items_fee_id" ON "tuition_fee_items" ("tuition_fee_id");--> statement-breakpoint
CREATE INDEX "idx_items_category_id" ON "tuition_fee_items" ("fee_category_id");--> statement-breakpoint
CREATE INDEX "idx_fees_student_id" ON "tuition_fees" ("student_id");--> statement-breakpoint
ALTER TABLE "payments" ADD CONSTRAINT "payments_tuition_fee_id_tuition_fees_id_fkey" FOREIGN KEY ("tuition_fee_id") REFERENCES "tuition_fees"("id") ON UPDATE CASCADE;--> statement-breakpoint
ALTER TABLE "payments" ADD CONSTRAINT "payments_student_id_students_id_fkey" FOREIGN KEY ("student_id") REFERENCES "students"("id") ON UPDATE CASCADE;--> statement-breakpoint
ALTER TABLE "students" ADD CONSTRAINT "students_user_id_users_id_fkey" FOREIGN KEY ("user_id") REFERENCES "users"("id") ON DELETE CASCADE ON UPDATE CASCADE;--> statement-breakpoint
ALTER TABLE "tuition_fee_items" ADD CONSTRAINT "tuition_fee_items_tuition_fee_id_tuition_fees_id_fkey" FOREIGN KEY ("tuition_fee_id") REFERENCES "tuition_fees"("id") ON DELETE CASCADE ON UPDATE CASCADE;--> statement-breakpoint
ALTER TABLE "tuition_fee_items" ADD CONSTRAINT "tuition_fee_items_fee_category_id_fee_categories_id_fkey" FOREIGN KEY ("fee_category_id") REFERENCES "fee_categories"("id") ON UPDATE CASCADE;--> statement-breakpoint
ALTER TABLE "tuition_fees" ADD CONSTRAINT "tuition_fees_student_id_students_id_fkey" FOREIGN KEY ("student_id") REFERENCES "students"("id") ON DELETE CASCADE ON UPDATE CASCADE;