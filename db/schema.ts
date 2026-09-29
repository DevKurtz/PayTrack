// PayTrack schema, ported from the MySQL dump (database/paytrack.sql) to Postgres.
import {
  pgTable,
  pgEnum,
  serial,
  integer,
  varchar,
  text,
  numeric,
  boolean,
  timestamp,
  date,
  index,
} from "drizzle-orm/pg-core";

export const userRole = pgEnum("user_role", ["admin", "accounting", "student"]);
export const userStatus = pgEnum("user_status", ["active", "inactive", "suspended"]);
export const tuitionStatus = pgEnum("tuition_status", ["unpaid", "partial", "paid"]);
export const paymentMethod = pgEnum("payment_method", [
  "cash",
  "gcash",
  "maya",
  "bank_transfer",
  "online",
  "card",
  "other",
]);
export const emailType = pgEnum("email_type", [
  "account_created",
  "accounting_account_created",
  "new_student_accounting_alert",
  "tuition_assessed",
  "payment_confirmation",
  "payment_reminder",
  "other",
]);
export const emailStatus = pgEnum("email_status", ["sent", "failed"]);

export const users = pgTable("users", {
  id: serial().primaryKey(),
  username: varchar({ length: 100 }).notNull().unique(),
  name: varchar({ length: 100 }),
  email: varchar({ length: 150 }),
  passwordHash: varchar("password_hash", { length: 255 }).notNull(),
  role: userRole().notNull().default("student"),
  isFirstLogin: boolean("is_first_login").notNull().default(true),
  status: userStatus().notNull().default("active"),
  lastLoginAt: timestamp("last_login_at"),
  lastActiveAt: timestamp("last_active_at"),
  createdAt: timestamp("created_at").notNull().defaultNow(),
  updatedAt: timestamp("updated_at").notNull().defaultNow().$onUpdate(() => new Date()),
});

export const students = pgTable(
  "students",
  {
    id: serial().primaryKey(),
    userId: integer("user_id")
      .notNull()
      .references(() => users.id, { onDelete: "cascade", onUpdate: "cascade" }),
    studentId: varchar("student_id", { length: 30 }).notNull().unique(),
    firstName: varchar("first_name", { length: 80 }).notNull(),
    lastName: varchar("last_name", { length: 80 }).notNull(),
    middleName: varchar("middle_name", { length: 80 }),
    email: varchar({ length: 150 }).notNull(),
    gradeLevel: varchar("grade_level", { length: 50 }),
    schoolYear: varchar("school_year", { length: 20 }),
    parentName: varchar("parent_name", { length: 160 }),
    parentEmail: varchar("parent_email", { length: 150 }),
    contactNumber: varchar("contact_number", { length: 20 }),
    address: text(),
    createdAt: timestamp("created_at").notNull().defaultNow(),
    updatedAt: timestamp("updated_at").notNull().defaultNow().$onUpdate(() => new Date()),
  },
  (t) => [index("idx_students_user_id").on(t.userId)],
);

export const feeCategories = pgTable("fee_categories", {
  id: serial().primaryKey(),
  name: varchar({ length: 100 }).notNull(),
  code: varchar({ length: 50 }).notNull().unique(),
  defaultAmount: numeric("default_amount", { precision: 12, scale: 2 }).notNull().default("0.00"),
  isVariable: boolean("is_variable").notNull().default(false),
  sortOrder: integer("sort_order").notNull().default(99),
  isActive: boolean("is_active").notNull().default(true),
  createdAt: timestamp("created_at").notNull().defaultNow(),
});

export const tuitionFees = pgTable(
  "tuition_fees",
  {
    id: serial().primaryKey(),
    studentId: integer("student_id")
      .notNull()
      .references(() => students.id, { onDelete: "cascade", onUpdate: "cascade" }),
    schoolYear: varchar("school_year", { length: 20 }).notNull(),
    semester: varchar({ length: 30 }).notNull().default("1st Semester"),
    description: varchar({ length: 200 }).notNull(),
    totalAmount: numeric("total_amount", { precision: 12, scale: 2 }).notNull().default("0.00"),
    amountPaid: numeric("amount_paid", { precision: 12, scale: 2 }).notNull().default("0.00"),
    dueDate: date("due_date"),
    status: tuitionStatus().notNull().default("unpaid"),
    createdAt: timestamp("created_at").notNull().defaultNow(),
    updatedAt: timestamp("updated_at").notNull().defaultNow().$onUpdate(() => new Date()),
  },
  (t) => [index("idx_fees_student_id").on(t.studentId)],
);

export const tuitionFeeItems = pgTable(
  "tuition_fee_items",
  {
    id: serial().primaryKey(),
    tuitionFeeId: integer("tuition_fee_id")
      .notNull()
      .references(() => tuitionFees.id, { onDelete: "cascade", onUpdate: "cascade" }),
    feeCategoryId: integer("fee_category_id")
      .notNull()
      .references(() => feeCategories.id, { onUpdate: "cascade" }),
    categoryName: varchar("category_name", { length: 100 }).notNull(),
    amount: numeric({ precision: 12, scale: 2 }).notNull().default("0.00"),
  },
  (t) => [
    index("idx_items_fee_id").on(t.tuitionFeeId),
    index("idx_items_category_id").on(t.feeCategoryId),
  ],
);

export const payments = pgTable(
  "payments",
  {
    id: serial().primaryKey(),
    tuitionFeeId: integer("tuition_fee_id")
      .notNull()
      .references(() => tuitionFees.id, { onUpdate: "cascade" }),
    studentId: integer("student_id")
      .notNull()
      .references(() => students.id, { onUpdate: "cascade" }),
    orNumber: varchar("or_number", { length: 50 }).notNull().unique(),
    amount: numeric({ precision: 12, scale: 2 }).notNull(),
    paymentMethod: paymentMethod("payment_method").notNull().default("online"),
    notes: text(),
    paidAt: timestamp("paid_at").notNull().defaultNow(),
    createdAt: timestamp("created_at").notNull().defaultNow(),
  },
  (t) => [
    index("idx_payments_fee_id").on(t.tuitionFeeId),
    index("idx_payments_student_id").on(t.studentId),
  ],
);

export const emailLogs = pgTable("email_logs", {
  id: serial().primaryKey(),
  recipientEmail: varchar("recipient_email", { length: 150 }).notNull(),
  subject: varchar({ length: 255 }).notNull(),
  type: emailType().notNull(),
  relatedId: integer("related_id"),
  status: emailStatus().notNull().default("sent"),
  errorMessage: text("error_message"),
  sentAt: timestamp("sent_at").notNull().defaultNow(),
});
